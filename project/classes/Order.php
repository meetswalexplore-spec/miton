<?php
/**
 * classes/Order.php
 */
require_once __DIR__ . '/../config/database.php';

class Order {
    private PDO $db;

    // Status constants
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_FAILED     = 'failed';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /** Create a new order record */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO orders
             (user_code, service_type, price, status, nid_number, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([
            $data['user_code']    ?? '',
            $data['service_type'] ?? 'smart_card_pdf',
            $data['price']        ?? 0,
            $data['status']       ?? self::STATUS_PENDING,
            $data['nid_number']   ?? '',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Fetch a single order by ID */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Update order status */
    public function updateStatus(int $id, string $status, ?string $outputPath = null): bool {
        $stmt = $this->db->prepare(
            'UPDATE orders SET status = ?, output_path = ?, updated_at = NOW() WHERE id = ?'
        );
        return $stmt->execute([$status, $outputPath, $id]);
    }

    /** Get order history for a user (latest first) */
    public function getHistoryByUser(string $code, int $limit = 50): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM orders WHERE user_code = ? ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->execute([$code, $limit]);
        return $stmt->fetchAll();
    }

    /** Count orders for a user */
    public function countByUser(string $code): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM orders WHERE user_code = ?');
        $stmt->execute([$code]);
        return (int)$stmt->fetchColumn();
    }
}
