<?php

class TransactionRepository
{
    public function __construct(private PDO $db) {}

    public function create(
        string $type,
        float $amount,
        ?int $fromAccountId,
        ?int $toAccountId
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO transactions (type, amount, from_account_id, to_account_id)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$type, $amount, $fromAccountId, $toAccountId]);
        return (int) $this->db->lastInsertId();
    }

    public function findByAccountId(int $accountId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, type, amount, from_account_id, to_account_id, created_at
             FROM transactions
             WHERE from_account_id = ? OR to_account_id = ?
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->execute([$accountId, $accountId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function countByAccountId(int $accountId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM transactions
             WHERE from_account_id = ? OR to_account_id = ?'
        );
        $stmt->execute([$accountId, $accountId]);
        return (int) $stmt->fetchColumn();
    }

    public function findAll(
        ?string $type = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $limit = 20,
        int $offset = 0
    ): array {
        $where = [];
        $params = [];

        if ($type) {
            $where[] = 'type = ?';
            $params[] = $type;
        }
        if ($dateFrom) {
            $where[] = 'DATE(created_at) >= ?';
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = 'DATE(created_at) <= ?';
            $params[] = $dateTo;
        }

        $sql = 'SELECT id, type, amount, from_account_id, to_account_id, created_at
                FROM transactions';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll(
        ?string $type = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): int {
        $where = [];
        $params = [];

        if ($type) {
            $where[] = 'type = ?';
            $params[] = $type;
        }
        if ($dateFrom) {
            $where[] = 'DATE(created_at) >= ?';
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = 'DATE(created_at) <= ?';
            $params[] = $dateTo;
        }

        $sql = 'SELECT COUNT(*) FROM transactions';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function countTransactions(mixed $accountId, int $userId): int
    {
        if ($accountId === 'all') {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM transactions 
                WHERE from_account_id IN (SELECT id FROM accounts WHERE user_id = ?) 
                   OR to_account_id IN (SELECT id FROM accounts WHERE user_id = ?)
            ");
            $stmt->execute([$userId, $userId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM transactions WHERE from_account_id = ? OR to_account_id = ?");
            $stmt->execute([$accountId, $accountId]);
        }
        
        return (int)$stmt->fetchColumn();
    }

    public function getTransactionsPaginated(mixed $accountId, int $userId, int $limit, int $offset): array
    {
        if ($accountId === 'all') {
            $stmt = $this->db->prepare("
                SELECT * FROM transactions 
                WHERE from_account_id IN (SELECT id FROM accounts WHERE user_id = :userId1) 
                   OR to_account_id IN (SELECT id FROM accounts WHERE user_id = :userId2) 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':userId1', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':userId2', $userId, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM transactions 
                WHERE from_account_id = :accId1 OR to_account_id = :accId2 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':accId1', $accountId, PDO::PARAM_INT);
            $stmt->bindValue(':accId2', $accountId, PDO::PARAM_INT);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

   public function findFilteredNoLimit($filterType, $filterDateFrom, $filterDateTo) {
    $sql = "SELECT * FROM transactions WHERE 1=1";
    $params = [];

    if (!empty($filterType)) {
        $sql .= " AND type = ?";
        $params[] = $filterType;
    }
    if (!empty($filterDateFrom)) {
        $sql .= " AND date >= ?";
        $params[] = $filterDateFrom;
    }
    if (!empty($filterDateTo)) {
        $sql .= " AND date <= ?";
        $params[] = $filterDateTo;
    }

    $sql .= " ORDER BY created_at DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
}
