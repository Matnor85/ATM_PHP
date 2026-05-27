<?php

class UserRepository
{
    public function __construct(private PDO $db, private AccountRepository $accountRepo) {}

    public function getUserById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, name, card_number, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findByCardNumber(string $cardNumber): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, card_number, pin_hash, role
             FROM users
             WHERE card_number = ?
             LIMIT 1'
        );
        $stmt->execute([$cardNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, card_number, role, created_at
             FROM users
             WHERE id = ?
             LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findWithCredentials(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, card_number, pin_hash, role 
             FROM users 
             WHERE id = ? 
             LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, card_number, role, created_at
             FROM users
             ORDER BY id ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $cardNumber, $pinHash, $role, $accountType, $initialDeposit) {
    $stmt = $this->db->prepare("INSERT INTO users (name, card_number, pin_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $cardNumber, $pinHash, $role]);
    $userId = (int)$this->db->lastInsertId();

    $this->accountRepo->createNewAccount($userId, $accountType, $initialDeposit);
    
    return $userId;
    }

    public function update(int $id, string $name, string $cardNumber, string $role): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET name = ?, card_number = ?, role = ?
             WHERE id = ?'
        );
        return $stmt->execute([$name, $cardNumber, $role, $id]);
    }

    public function updatePin(int $id, string $pinHash): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET pin_hash = ? WHERE id = ?'
        );
        return $stmt->execute([$pinHash, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function cardNumberExists(string $cardNumber, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare(
                'SELECT 1 FROM users WHERE card_number = ? AND id != ? LIMIT 1'
            );
            $stmt->execute([$cardNumber, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                'SELECT 1 FROM users WHERE card_number = ? LIMIT 1'
            );
            $stmt->execute([$cardNumber]);
        }
        return (bool) $stmt->fetch();
    }

    public function findAllUsersOnly(int $page = 1, int $limit = 5): array
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT id, name, card_number FROM users WHERE role = 'user' ORDER BY id DESC LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function countTotalUsers(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            return 0;
        }
    }
}