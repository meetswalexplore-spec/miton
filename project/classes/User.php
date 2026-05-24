<?php
/**
 * classes/User.php
 */
require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /** Find a user by their unique code (USRxxxxxxx) */
    public function findByCode(string $code): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_code = ? LIMIT 1');
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }

    /** Find a user by their ID */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Return the balance for a given user code */
    public function getBalance(string $code): float {
        $user = $this->findByCode($code);
        return $user ? (float)$user['balance'] : 0.0;
    }

    /** Deduct an amount from a user's balance.
     *  Returns true on success, false if balance is insufficient. */
    public function deductBalance(string $code, float $amount): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'SELECT balance FROM users WHERE user_code = ? FOR UPDATE'
            );
            $stmt->execute([$code]);
            $row = $stmt->fetch();

            if (!$row || (float)$row['balance'] < $amount) {
                $this->db->rollBack();
                return false;
            }

            $this->db->prepare(
                'UPDATE users SET balance = balance - ? WHERE user_code = ?'
            )->execute([$amount, $code]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Get auto-save address for a user */
    public function getSavedAddress(string $code): array {
        $stmt = $this->db->prepare(
            'SELECT saved_address, auto_save FROM users WHERE user_code = ? LIMIT 1'
        );
        $stmt->execute([$code]);
        $row = $stmt->fetch();
        return [
            'saved_address' => $row['saved_address'] ?? '',
            'auto_save'     => $row['auto_save']     ?? '0',
        ];
    }

    /** Persist auto-save address */
    public function saveAddress(string $code, string $address, int $autoSave): bool {
        $stmt = $this->db->prepare(
            'UPDATE users SET saved_address = ?, auto_save = ? WHERE user_code = ?'
        );
        return $stmt->execute([$address, $autoSave, $code]);
    }
}
