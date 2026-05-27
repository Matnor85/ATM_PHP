<?php

class TransactionService
{
    private PDO $db;
    private TransactionRepository $transactionRepo;

    public function __construct(PDO $db, TransactionRepository $transactionRepo)
    {
        $this->db = $db;
        $this->transactionRepo = $transactionRepo;
    }

    public function executeTransfer(int $fromAccountId, int $toAccountId, float $amount, int $userId): array
    {
    
    if (!$fromAccountId || !$toAccountId) {
        return ['success' => false, 'message' => 'Du måste välja både ett från-konto och ett till-konto.'];
    }
    if ($fromAccountId === $toAccountId) {
        return ['success' => false, 'message' => 'Du kan inte överföra till samma konto.'];
    }
    if ($amount <= 0) {
        return ['success' => false, 'message' => 'Beloppet måste vara större än 0 kr.'];
    }

    $stmt = $this->db->prepare("SELECT id, balance FROM accounts WHERE id = ? AND user_id = ?");
    $stmt->execute([$fromAccountId, $userId]);
    $fromAccount = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$fromAccount) {
        return ['success' => false, 'message' => 'Från-kontot är ogiltigt eller tillhör inte användaren.'];
    }

    $stmt = $this->db->prepare("SELECT id FROM accounts WHERE id = ?");
    $stmt->execute([$toAccountId]);
    $toAccount = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$toAccount) {
        return ['success' => false, 'message' => 'Till-kontot finns inte.'];
    }

    if ((float)$fromAccount['balance'] < $amount) {
        return ['success' => false, 'message' => 'Otillräckligt saldo på från-kontot.'];
    }

    try {
        $this->db->beginTransaction();

        $stmtUpdateFrom = $this->db->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
        $stmtUpdateFrom->execute([$amount, $fromAccountId]);

        $stmtUpdateTo = $this->db->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
        $stmtUpdateTo->execute([$amount, $toAccountId]);

        $this->transactionRepo->create('transfer', $amount, $fromAccountId, $toAccountId);

        $this->db->commit();
        return ['success' => true, 'message' => 'Överföringen på ' . format_money($amount) . ' lyckades!'];
        
    } catch (Exception $e) {
        $this->db->rollBack();
        return ['success' => false, 'message' => 'Ett systemfel uppstod. Inga pengar drogs.'];
    }
}

    public function executeWithdrawal(int $accountId, float $amount, int $userId): array
    {
        if (!$accountId) {
            return ['success' => false, 'message' => 'Du måste välja ett konto.'];
        }
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Beloppet måste vara större än 0 kr.'];
        }

        $stmt = $this->db->prepare("SELECT id, balance FROM accounts WHERE id = ? AND user_id = ?");
        $stmt->execute([$accountId, $userId]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$account) {
            return ['success' => false, 'message' => 'Ogiltigt konto.'];
        }
        if ((float)$account['balance'] < $amount) {
            return ['success' => false, 'message' => 'Otillräckligt saldo för detta uttag.'];
        }

        try {
            $this->db->beginTransaction();

            $stmtUpdate = $this->db->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
            $stmtUpdate->execute([$amount, $accountId]);

            $this->transactionRepo->create('withdrawal', $amount, $accountId, null);

            $this->db->commit();
            return ['success' => true, 'message' => 'Uttaget på ' . format_money($amount) . ' lyckades!'];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Ett systemfel uppstod. Inga pengar drogs.'];
        }
    }

    public function executeDeposit(int $accountId, float $amount, int $userId): array
    {
        if (!$accountId) {
            return ['success' => false, 'message' => 'Du måste välja ett konto.'];
        }
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Beloppet måste vara större än 0 kr.'];
        }

        $stmt = $this->db->prepare("SELECT id FROM accounts WHERE id = ? AND user_id = ?");
        $stmt->execute([$accountId, $userId]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$account) {
            return ['success' => false, 'message' => 'Ogiltigt konto.'];
        }

        try {
            $this->db->beginTransaction();

            $stmtUpdate = $this->db->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
            $stmtUpdate->execute([$amount, $accountId]);

            $this->transactionRepo->create('deposit', $amount, null, $accountId);

            $this->db->commit();
            return ['success' => true, 'message' => 'Insättningen på ' . format_money($amount) . ' lyckades!'];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Ett systemfel uppstod. Inga pengar sattes in.'];
        }
    }
}