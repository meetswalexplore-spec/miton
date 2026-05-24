<?php
/**
 * api/upload.php
 * Accepts a sign-copy PDF, forwards it to the NID data API,
 * and returns JSON with field values + photo / signature URLs.
 *
 * POST  /api/upload.php
 * Field: pdf_file  (multipart file, application/pdf, ≤ 10 MB)
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

// ── CORS (adjust origin in production) ───────────────────────────
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// ── Only POST allowed ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}

// ── Validate upload ───────────────────────────────────────────────
if (empty($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'PDF ফাইল আপলোড করা হয়নি বা ত্রুটি হয়েছে।']);
    exit;
}

$file = $_FILES['pdf_file'];

if ($file['size'] > MAX_UPLOAD_BYTES) {
    echo json_encode(['status' => 'error', 'message' => 'ফাইলের সাইজ বেশি (সর্বোচ্চ 10 MB)।']);
    exit;
}

$mimeType = mime_content_type($file['tmp_name']);
if ($mimeType !== ALLOWED_PDF_MIME) {
    echo json_encode(['status' => 'error', 'message' => 'শুধু PDF ফাইল গ্রহণযোগ্য।']);
    exit;
}

// ── Forward to NID sign-copy API ──────────────────────────────────
try {
    $cFile = new CURLFile($file['tmp_name'], ALLOWED_PDF_MIME, $file['name']);

    $ch = curl_init(SMART_NID_API_URL . '/extract');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => ['pdf_file' => $cFile],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_HTTPHEADER     => [
            // 'Authorization: Bearer YOUR_KEY',
        ],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        echo json_encode(['status' => 'error', 'message' => 'API থেকে সাড়া পাওয়া যায়নি।']);
        exit;
    }

    $data = json_decode($response, true);
    echo json_encode($data ?? ['status' => 'error', 'message' => 'API রেসপন্স পার্স করা যায়নি।']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
