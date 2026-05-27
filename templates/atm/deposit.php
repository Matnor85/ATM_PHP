<?php 
$pageTitle = 'Insättning'; 

$userId = $_SESSION['atm_user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountId = (int)($_POST['account_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);

    $result = $transactionService->executeDeposit($accountId, $amount, $userId);

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

<h1>Insättning</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="index.php?page=deposit">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="account_id">Välj konto</label>
            <select name="account_id" id="account_id" required>
                <option value="" disabled selected>-- Välj konto --</option>
                <?php foreach ($accounts as $acc): ?>
                <option value="<?= e($acc['id']) ?>">
                    <?= e(match($acc['account_type']) {
                        'checking' => 'Lönekonto',
                        'savings'  => 'Sparkonto',
                        'fixed'    => 'Fasträntekonto',
                        'credit'   => 'Kreditkonto',
                        default    => $acc['account_type'],
                    }) ?>
                    #<?= e($acc['id']) ?> — <?= format_money((float)$acc['balance']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Belopp (kr)</label>
            <input type="number" id="amount" name="amount" min="1" step="1" required placeholder="T.ex. 1000">
        </div>

        <button type="submit" class="btn btn-primary">Genomför insättning</button>
        <a href="index.php?page=dashboard" class="btn btn-secondary" style="margin-left: 0.5rem;">Avbryt</a>
    </form>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>