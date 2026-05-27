<?php 
$pageTitle = 'Uttag'; 

$userId = $_SESSION['atm_user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountId = (int)($_POST['account_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);

    $result = $transactionService->executeWithdrawal($accountId, $amount, $userId);

    if ($result['success']) {
        $success = $result['message'];
        $_POST = []; 
    } else {
        $error = $result['message'];
    }
}

$accounts = $accountRepo->findByUserId($userId);
if (!is_array($accounts)) {
    $accounts = []; 
}
?>

<h1>Uttag</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="index.php?page=withdraw">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="account_id">Välj konto</label>
            <select name="account_id" id="account_id" required>
                <option value="" disabled <?= !isset($_POST['to_account_id']) ? 'selected' : '' ?>>-- Välj konto --</option>
                <?php foreach ($accounts as $acc): ?>
                    <?php if (in_array($acc['account_type'], ['checking', 'savings', 'fixed', 'credit'])): ?>
                    <option value="<?= e($acc['id']) ?>"
                        <?= (($_POST['account_id'] ?? '') == $acc['id']) ? 'selected' : '' ?>>
                        <?= e(match($acc['account_type']) {
                            'checking' => 'Lönekonto',
                            'savings'  => 'Sparkonto',
                            'credit'   => 'Kreditkonto',
                            default    => $acc['account_type'],
                        }) ?>
                        : <?= format_money((float)$acc['balance']) ?>
                    </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Belopp (kr)</label>
            <input
                type="number"
                id="amount"
                name="amount"
                min="1"
                step="1"
                required
                placeholder="T.ex. 500"
                value="<?= e($_POST['amount'] ?? '') ?>"
            >
        </div>

        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.2rem; flex-wrap: wrap;">
            <?php foreach ([100, 200, 500, 1000, 2000] as $quick): ?>
            <button type="button"
                    class="btn btn-secondary"
                    style="font-size: 0.85rem; padding: 6px 14px;"
                    onclick="document.getElementById('amount').value = '<?= $quick ?>'">
                <?= $quick ?> kr
            </button>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary">Genomför uttag</button>
        <a href="index.php?page=dashboard" class="btn btn-secondary" style="margin-left: 0.5rem;">Avbryt</a>
    </form>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>
