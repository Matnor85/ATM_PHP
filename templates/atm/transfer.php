<?php 
$pageTitle = 'Överföring'; 

$userId = $_SESSION['atm_user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromAccountId = (int)($_POST['from_account_id'] ?? 0);
    $toAccountId = (int)($_POST['to_account_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);

    $result = $transactionService->executeTransfer($fromAccountId, $toAccountId, $amount, $userId);

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

<h1>Överföring</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="index.php?page=transfer">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="from_account_id">Från konto</label>
            <select name="from_account_id" id="from_account_id" required>
                <option value="" disabled <?= !isset($_POST['from_account_id']) ? 'selected' : '' ?>>-- Välj konto --</option>
                
                <?php foreach ($accounts as $acc): ?>
                <option value="<?= e($acc['id']) ?>"
                    <?= (($_POST['from_account_id'] ?? '') == $acc['id']) ? 'selected' : '' ?>>
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
            <label for="to_account_id">Till konto</label>
            <select name="to_account_id" id="to_account_id" required>
                <option value="" disabled <?= !isset($_POST['to_account_id']) ? 'selected' : '' ?>>-- Välj konto --</option>
                
                <?php foreach ($accounts as $acc): ?>
                <option value="<?= e($acc['id']) ?>"
                    <?= (($_POST['to_account_id'] ?? '') == $acc['id']) ? 'selected' : '' ?>>
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

        <button type="submit" class="btn btn-primary">Genomför överföring</button>
        <a href="index.php?page=dashboard" class="btn btn-secondary" style="margin-left: 0.5rem;">Avbryt</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromSelect = document.getElementById('from_account_id');
    const toSelect = document.getElementById('to_account_id');

    function filtreraKonton() {
        const valtFranId = fromSelect.value;

        Array.from(toSelect.options).forEach(option => {
            if (option.value === valtFranId && valtFranId !== "") {
                option.style.display = 'none';  // Göm alternativet
                option.disabled = true;         // Säkerhetsspärr: gör det o-väljbart
                
                if (toSelect.value === valtFranId) {
                    toSelect.value = "";
                }
            } else {
                if (option.value !== "") {
                    option.style.display = 'block';
                    option.disabled = false;
                }
            }
        });
    }

    fromSelect.addEventListener('change', filtreraKonton);

    if (fromSelect.value) {
        filtreraKonton();
    }
});
</script>

<?php require __DIR__ . '/../layout_footer.php'; ?>