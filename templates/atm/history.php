<?php 
$pageTitle = 'Transaktionshistorik'; 

$userId = $_SESSION['atm_user_id'];
$accounts = $accountRepo->findByUserId($userId);
if (!is_array($accounts)) {
    $accounts = []; 
}

$selectedAccountId = $_GET['account_id'] ?? 'all';

$validAccount = false;
if ($selectedAccountId === 'all') {
    $validAccount = true;
} else {
    $selectedAccountId = (int)$selectedAccountId;
    foreach ($accounts as $acc) {
        if ($acc['id'] == $selectedAccountId) {
            $validAccount = true;
            break;
        }
    }
}

$transactions = [];
$currentPage = max(1, (int)($_GET['p'] ?? 1));
$limit = 10; 
$totalPages = 0;

if ($validAccount) {
    $totalRecords = $transactionRepo->countTransactions($selectedAccountId, $userId);
    
    $totalPages = ceil($totalRecords / $limit);
    $offset = ($currentPage - 1) * $limit;

    $transactions = $transactionRepo->getTransactionsPaginated($selectedAccountId, $userId, $limit, $offset);
}
?>

<h1>Transaktionshistorik</h1>

<div class="card">
    <form method="GET" action="index.php" class="filter-form">
        <input type="hidden" name="page" value="history">
        <div>
            <label for="account_id" class="filter-label">Välj konto</label>
            <select name="account_id" id="account_id" onchange="this.form.submit()" class="filter-select">
                <option value="all" <?= ($selectedAccountId === 'all') ? 'selected' : '' ?>>-- Alla konton --</option>
                <?php foreach ($accounts as $acc): ?>
                <option value="<?= e($acc['id']) ?>" <?= ($selectedAccountId == $acc['id']) ? 'selected' : '' ?>>
                    <?= e(match($acc['account_type']) {
                        'checking' => 'Lönekonto',
                        'savings'  => 'Sparkonto',
                        'fixed'    => 'Fasträntekonto',
                        'credit'   => 'Kreditkonto',
                        default    => $acc['account_type'],
                    }) ?>
                    #<?= e($acc['id']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if (empty($transactions)): ?>
        <p class="empty-state">Inga transaktioner hittades.</p>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Typ</th>
                <th>Belopp</th>
                <th>Från konto</th>
                <th>Till konto</th>
                <th>Datum</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $tx): ?>
            <tr>
                <td>
                    <span class="badge-label">
                        <?= e(match($tx['type']) {
                            'deposit' => 'Insättning',
                            'withdrawal' => 'Uttag',
                            'transfer' => 'Överföring',
                            default => $tx['type']
                        }) ?>
                    </span>
                </td>
                <td class="text-bold"><?= format_money((float)$tx['amount']) ?></td>
                <td class="text-muted"><?= $tx['from_account_id'] ? '#' . e($tx['from_account_id']) : '—' ?></td>
                <td class="text-muted"><?= $tx['to_account_id']   ? '#' . e($tx['to_account_id'])   : '—' ?></td>
                <td class="text-muted"><?= e(date('Y-m-d H:i', strtotime($tx['created_at']))) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div class="pagination-wrapper">
        <?php if ($currentPage > 1): ?>
            <a href="?page=history&account_id=<?= e($selectedAccountId) ?>&p=<?= $currentPage - 1 ?>" class="btn btn-secondary">← Föregående</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $currentPage): ?>
                <span class="btn btn-primary"><?= $i ?></span>
            <?php else: ?>
                <a href="?page=history&account_id=<?= e($selectedAccountId) ?>&p=<?= $i ?>" class="btn btn-secondary"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=history&account_id=<?= e($selectedAccountId) ?>&p=<?= $currentPage + 1 ?>" class="btn btn-secondary">Nästa →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>