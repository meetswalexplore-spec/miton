<?php
/**
 * classes/PDFGenerator.php
 * Handles smart-card PDF generation via an external API or local TCPDF.
 */
require_once __DIR__ . '/../config/constants.php';

class PDFGenerator {

    private string $outputDir;

    public function __construct() {
        $this->outputDir = OUTPUT_PATH . '/pdfs';
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    /**
     * Generate a smart-card PDF.
     *
     * @param  array  $formData  Validated form fields
     * @param  string $photoPath Absolute server path to the profile photo
     * @param  string $signPath  Absolute server path to the signature image
     * @return array  ['status'=>'success'|'error', 'file'=>'...', 'url'=>'...', 'message'=>'...']
     */
    public function generateSmartCard(array $formData, string $photoPath, string $signPath): array {
        try {
            $filename   = $this->buildFilename($formData['nid_num'] ?? 'unknown');
            $outputFile = $this->outputDir . '/' . $filename;

            // ── Build PDF using cURL to external API ──────────────────
            $payload = $this->buildPayload($formData, $photoPath, $signPath);
            $pdfBytes = $this->callExternalApi($payload);

            if (!$pdfBytes) {
                return ['status' => 'error', 'message' => 'PDF API থেকে কোনো ডেটা আসেনি।'];
            }

            file_put_contents($outputFile, $pdfBytes);

            return [
                'status'  => 'success',
                'file'    => $outputFile,
                'url'     => OUTPUT_URL . '/pdfs/' . $filename,
                'message' => 'PDF তৈরি হয়েছে।',
            ];

        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function buildFilename(string $nid): string {
        return 'smart_card_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nid)
             . '_' . date('YmdHis') . '.pdf';
    }

    private function buildPayload(array $d, string $photoPath, string $signPath): array {
        return [
            'name_bn'      => $d['nameBN']       ?? '',
            'name_en'      => $d['nameEn']        ?? '',
            'nid_number'   => $d['nid_num']       ?? '',
            'birth_date'   => $d['dob_date']      ?? '',
            'father'       => $d['father_name']   ?? '',
            'mother'       => $d['mother_name']   ?? '',
            'birth_place'  => $d['birth_place']   ?? '',
            'pin'          => $d['pincode']        ?? '',
            'blood_group'  => $d['blood_groud']   ?? '',
            'gender'       => $d['gender']         ?? '',
            'issue_date'   => $d['regs_date']     ?? '',
            'address'      => $d['address']        ?? '',
            'photo'        => $this->fileToBase64($photoPath),
            'signature'    => $this->fileToBase64($signPath),
        ];
    }

    private function fileToBase64(string $path): string {
        if (!$path || !file_exists($path)) return '';
        $mime = mime_content_type($path) ?: 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    /**
     * Call the external PDF-generation API.
     * Replace the URL / auth header with your real credentials.
     */
    private function callExternalApi(array $payload): ?string {
        $ch = curl_init(SMART_NID_API_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/pdf',
                // 'Authorization: Bearer YOUR_KEY',
            ],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode === 200 && $response) ? $response : null;
    }
}
