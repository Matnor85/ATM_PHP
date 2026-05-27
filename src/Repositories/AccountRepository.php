<?php

class AccountRepository
{
    public function __construct(private PDO $db) {}

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, user_id, account_type, balance, credit_limit,
                    interest_rate, locked_until, active, created_at
             FROM accounts
             WHERE user_id = ?
             ORDER BY id ASC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, user_id, account_type, balance, credit_limit,
                    interest_rate, locked_until, active, created_at
             FROM accounts
             WHERE id = ?
             LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT a.id, u.name AS owner_name, a.account_type,
                    a.balance, a.credit_limit, a.active, a.created_at
             FROM accounts a
             JOIN users u ON u.id = a.user_id
             ORDER BY a.id ASC'
        );
        return $stmt->fetchAll();
    }

    public function deposit(int $accountId, float $amount): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE accounts
             SET balance = balance + ?
             WHERE id = ?'
        );
        return $stmt->execute([$amount, $accountId]);
    }

    public function withdraw(int $accountId, float $amount): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE accounts
             SET balance = balance - ?
             WHERE id = ? AND balance >= ?'
        );
        $stmt->execute([$amount, $accountId, $amount]);
        return $stmt->rowCount() === 1;
    }

    public function belongsToUser(int $accountId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM accounts
             WHERE id = ? AND user_id = ?
             LIMIT 1'
        );
        $stmt->execute([$accountId, $userId]);
        return (bool) $stmt->fetch();
    }
    public function getCustomersOverview(string $sort = 'balance', string $order = 'DESC'): array
    {
        $allowedSort = ['owner_name', 'account_count', 'balance'];
        $sort = in_array($sort, $allowedSort) ? $sort : 'balance';
        
        if ($sort === 'owner_name') $sort = 'users.name';
        if ($sort === 'account_count') $sort = 'account_count';
        if ($sort === 'balance') $sort = 'total_balance';

        $order = ($order === 'ASC') ? 'ASC' : 'DESC';

        $sql = "SELECT 
                    users.id AS user_id,
                    users.name AS owner_name,
                    COUNT(accounts.id) AS account_count,
                    SUM(accounts.balance) AS total_balance
                FROM users
                LEFT JOIN accounts ON users.id = accounts.user_id
                GROUP BY users.id, users.name
                ORDER BY {$sort} {$order}";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccountsByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, account_type, balance, interest_rate, active, created_at 
             FROM accounts 
             WHERE user_id = ?
             ORDER BY id ASC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateAccountStatus(int $accountId, int $status): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE accounts 
             SET active = ? 
             WHERE id = ?"
        );
        return $stmt->execute([$status, $accountId]);
    }

    public function createNewAccount(int $userId, string $accountType, float $balance): bool
    {
    $stmt = $this->db->prepare("
        INSERT INTO accounts (user_id, account_type, balance, active) 
        VALUES (?, ?, ?, 1)
    ");
    return $stmt->execute([$userId, $accountType, $balance]);
    }

    public function deleteAccount(int $accountId): bool
    {
    $stmt = $this->db->prepare("SELECT balance FROM accounts WHERE id = ?");
    $stmt->execute([$accountId]);
    $account = $stmt->fetch();

    if ($account && (float)$account['balance'] > 0) {
        throw new Exception("Kan inte ta bort konto: Kontot har ett saldo på " . $account['balance'] . " kr.");
    }

    $stmt = $this->db->prepare("DELETE FROM accounts WHERE id = ?");
    return $stmt->execute([$accountId]);
    }
}
