<?php 
$pageTitle = 'Dashboard'; 
$userId = $_SESSION['atm_user_id'];
$user = $userRepo->findById($userId);
$userName = $user['name'] ?? 'Användare';
$accounts = $accountRepo->findByUserId($userId) ?: [];

$transactionRepo = new TransactionRepository($db);
$recentTransactions = [];
if (!empty($accounts)) {
    $recentTransactions = $transactionRepo->findByAccountId($accounts[0]['id'], 5, 0);
}
?>

<h1>Välkommen, <?= e($userName) ?></h1>
<div class="dashboard-layout">
    <div class="account-grid">
        <?php foreach ($accounts as $acc): ?>
        <div class="card dashboard-card">
            <div class="account-card-header">
                <div>
                    <div class="account-type-label">
                        <?= e(match($acc['account_type']) {
                            'checking' => 'Lönekonto',
                            'savings'  => 'Sparkonto',
                            'fixed'    => 'Fasträntekonto',
                            'credit'   => 'Kreditkonto',
                            default    => $acc['account_type'],
                        }) ?>
                    </div>
                    <div class="account-id">Konto #<?= e($acc['id']) ?></div>
                </div>
                <div class="account-icon">
                    <?= $acc['account_type'] === 'credit' ? '💳' : '🏦' ?>
                </div>
            </div>
            <div class="balance-text"><?= format_money((float)$acc['balance']) ?></div>
            <?php if ($acc['interest_rate'] > 0): ?>
            <div class="interest-text">Ränta: <?= e($acc['interest_rate']) ?>%</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>


    <div class="card">
        <h2>Snabbval</h2>
        <div class="quick-actions">
            <a href="index.php?page=withdraw" class="btn btn-primary">Uttag</a>
            <a href="index.php?page=deposit"  class="btn btn-primary">Insättning</a>
            <a href="index.php?page=transfer" class="btn btn-primary">Överföring</a>
            <a href="index.php?page=history"  class="btn btn-secondary">Transaktionshistorik</a>
            <a href="index.php?page=change_pin" class="btn btn-secondary">Byt PIN-kod</a>
        </div>
    </div>
</div>
<?php if (!empty($recentTransactions)): ?>
<div class="card">
    <h2>Senaste transaktioner</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Typ</th>
                <th>Belopp</th>
                <th>Datum</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentTransactions as $tx): ?>
            <tr>
                <td>
                    <span class="badge badge-<?= e($tx['type']) ?>">
                        <?= e(tx_type_label($tx['type'])) ?>
                    </span>
                </td>
                <td class="text-bold"><?= format_money((float)$tx['amount']) ?></td>
                <td class="text-muted"><?= format_date($tx['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="transaction-link">
        <a href="index.php?page=history" class="btn btn-secondary">Visa all historik →</a>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout_footer.php'; ?>