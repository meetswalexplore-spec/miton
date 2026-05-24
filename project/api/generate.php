<?php
/**
 * api/generate.php
 * Validates form data, deducts balance, generates the smart-card PDF,
 * logs the order, and returns a signed download URL.
 *
 * POST  /api/generate.php
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/PDFGenerator.php';

// ── Only POST ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}

// ── Helpers ───────────────────────────────────────────────────────
function jsonError(string $msg, int $code = 400): never {
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

function sanitize(string $v): string {
    return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
}

// ── Read & sanitize inputs ─────────────────────────────────────────
$userCode  = sanitize($_POST['user_id']     ?? '');
$nameBN    = sanitize($_POST['nameBN']      ?? '');
$nameEn    = sanitize($_POST['nameEn']      ?? '');
$nidNum    = sanitize($_POST['nid_num']     ?? '');
$dobDate   = sanitize($_POST['dob_date']    ?? '');
$fatherName= sanitize($_POST['father_name'] ?? '');
$motherName= sanitize($_POST['mother_name'] ?? '');
$birthPlace= sanitize($_POST['birth_place'] ?? '');
$pincode   = sanitize($_POST['pincode']     ?? '');
$bloodGroup= sanitize($_POST['blood_groud'] ?? '');
$gender    = sanitize($_POST['gender']      ?? '');
$regsDate  = sanitize($_POST['regs_date']   ?? '');
$address   = sanitize($_POST['address']     ?? '');
$hiddenPhoto = trim($_POST['hiddenProfileImg'] ?? '');
$hiddenSign  = trim($_POST['hiddenSignImg']    ?? '');
$uCode       = sanitize($_POST['hiddenUCode']  ?? '');

// ── Required field validation ──────────────────────────────────────
if (!$userCode) jsonError('User ID missing.');
if (!$nameBN || !$nameEn) jsonError('নাম পূরণ করুন।');
if (!$nidNum)  jsonError('এনআইডি নম্বর দিন।');
if (!$dobDate) jsonError('জন্ম তারিখ দিন।');
if (!$gender)  jsonError('লিঙ্গ সিলেক্ট করুন।');

// ── Date format check: "06 Jan 2025" ──────────────────────────────
$dateRegex = '/^(0[1-9]|[12][0-9]|3[01])\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(19|20)\d{2}$/';
if (!preg_match($dateRegex, $dobDate))  jsonError('জন্ম তারিখ সঠিক ফরম্যাটে দিন। (উদাহরণ: 06 Jan 2025)');
if ($regsDate && !preg_match($dateRegex, $regsDate)) jsonError('প্রদানের তারিখ সঠিক ফরম্যাটে দিন।');

// ── Birth place: ALL CAPS English ─────────────────────────────────
if ($birthPlace && !preg_match("/^[A-Z][A-Z .'\\-]*$/", $birthPlace)) {
    jsonError('জন্মস্থান ইংরেজিতে বড় হাতে লিখুন। (উদাহরণ: DHAKA)');
}

// ── Verify user ────────────────────────────────────────────────────
$userModel = new User();
$user = $userModel->findByCode($userCode);
if (!$user) jsonError('অ্যাকাউন্ট পাওয়া যায়নি।', 403);

// ── Deduct balance ─────────────────────────────────────────────────
$price = PRICE_SMART_CARD_PDF;
if (!$userModel->deductBalance($userCode, $price)) {
    jsonError('অপর্যাপ্ত ব্যালেন্স। রিচার্জ করুন।', 402);
}

// ── Handle images ──────────────────────────────────────────────────
// Images may arrive as uploaded files OR as hidden base64/URL values
$photoPath = '';
$signPath  = '';
$tmpFiles  = [];

function saveBase64OrUrl(string $data, string $prefix): string {
    if (!$data) return '';
    $tmpPath = sys_get_temp_dir() . '/' . $prefix . '_' . uniqid() . '.jpg';
    if (str_starts_with($data, 'data:')) {
        [, $base64] = explode(',', $data, 2);
        file_put_contents($tmpPath, base64_decode($base64));
    } elseif (filter_var($data, FILTER_VALIDATE_URL)) {
        $bytes = @file_get_contents($data);
        if ($bytes) file_put_contents($tmpPath, $bytes);
        else return '';
    } else {
        return '';
    }
    return $tmpPath;
}

if (!empty($_FILES['profile_img']['tmp_name'])) {
    $photoPath = $_FILES['profile_img']['tmp_name'];
} elseif ($hiddenPhoto) {
    $photoPath = saveBase64OrUrl($hiddenPhoto, 'photo');
    if ($photoPath) $tmpFiles[] = $photoPath;
}

if (!empty($_FILES['sign_img']['tmp_name'])) {
    $signPath = $_FILES['sign_img']['tmp_name'];
} elseif ($hiddenSign) {
    $signPath = saveBase64OrUrl($hiddenSign, 'sign');
    if ($signPath) $tmpFiles[] = $signPath;
}

// ── Generate PDF ───────────────────────────────────────────────────
$generator = new PDFGenerator();
$result = $generator->generateSmartCard(
    compact('nameBN','nameEn','nid_num','dob_date','father_name','mother_name',
            'birth_place','pincode','blood_groud','gender','regs_date','address'),
    $photoPath,
    $signPath
);

// Clean up temp files
foreach ($tmpFiles as $f) { if (file_exists($f)) @unlink($f); }

if ($result['status'] !== 'success') {
    // Refund on failure
    $pdo = Database::getInstance();
    $pdo->prepare('UPDATE users SET balance = balance + ? WHERE user_code = ?')
        ->execute([$price, $userCode]);
    jsonError($result['message'], 500);
}

// ── Log order ──────────────────────────────────────────────────────
$order = new Order();
$orderId = $order->create([
    'user_code'    => $userCode,
    'service_type' => 'smart_card_pdf',
    'price'        => $price,
    'status'       => Order::STATUS_COMPLETED,
    'nid_number'   => $nidNum,
]);
$order->updateStatus($orderId, Order::STATUS_COMPLETED, $result['file']);

// ── Respond ────────────────────────────────────────────────────────
echo json_encode([
    'status'   => 'success',
    'message'  => 'স্মার্টকার্ড PDF তৈরি হয়েছে।',
    'redirect' => $result['url'],
    'order_id' => $orderId,
]);
