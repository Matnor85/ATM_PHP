<?php

class UserService
{
    private PDO $db;
    private UserRepository $userRepo;
    private AuditRepository $auditRepo;

    public function __construct(PDO $db, UserRepository $userRepo, AuditRepository $auditRepo)
    {
        $this->db = $db;
        $this->userRepo = $userRepo;
        $this->auditRepo = $auditRepo;
    }

    public function changePin(int $userId, string $oldPin, string $newPin, string $newPinConfirm): array
    {
        if (empty($oldPin) || empty($newPin) || empty($newPinConfirm)) {
            return ['success' => false, 'message' => 'Alla fält måste fyllas i.'];
        }
        if ($newPin !== $newPinConfirm) {
            return ['success' => false, 'message' => 'De nya pinkoderna matchar inte.'];
        }
        if (strlen($newPin) < 4 || !is_numeric($newPin)) {
            return ['success' => false, 'message' => 'Den nya pinkoden måste bestå av minst 4 siffror.'];
        }

        $user = $this->userRepo->findWithCredentials($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Användaren hittades inte.'];
        }

        if (!password_verify($oldPin, $user['pin_hash'])) {
            return ['success' => false, 'message' => 'Den nuvarande pinkoden är felaktig.'];
        }

        $newPinHash = password_hash($newPin, PASSWORD_DEFAULT);
        
        try {
            $stmt = $this->db->prepare("UPDATE users SET pin_hash = ? WHERE id = ?");
            $stmt->execute([$newPinHash, $userId]);
            
            return ['success' => true, 'message' => 'Din pinkod har nu ändrats!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Ett systemfel uppstod. Pinkoden kunde inte ändras.'];
        }
    }
 
    public function update(int $userId, string $name, string $cardNumber, string $role) {
    return $this->userRepo->update($userId, $name, $cardNumber, $role);
    }


    public function create(string $name, string $pin, string $accountType, string $role, float $initialDeposit) {
    $pinHash = password_hash($pin, PASSWORD_DEFAULT);
    $cardNumber = '6500' . rand(100000000000, 999999999999); 
    
    return $this->userRepo->create($name, $cardNumber, $pinHash, $role, $accountType, $initialDeposit);
    }
   
    public function delete(int $userId): void
    {
    $stmt = $this->db->prepare("SELECT SUM(balance) FROM accounts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalBalance = (float)$stmt->fetchColumn();

    if ($totalBalance > 0) {
        throw new Exception("Kan inte ta bort användare: Användaren har pengar kvar på sina konton (" . $totalBalance . " kr).");
    }

    $this->db->prepare("DELETE FROM accounts WHERE user_id = ?")->execute([$userId]);
    $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    
    $this->auditRepo->log($_SESSION['user_id'] ?? null, 'admin_delete_user', "Tog bort användare ID: $userId");
    }
}