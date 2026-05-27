<?php 
$pageTitle = 'Mina Konton'; 

$userId = $_SESSION['atm_user_id'];
$accounts = $accountRepo->findByUserId($userId);
if (!is_array($accounts)) {
    $accounts = []; 
}

$totalBalance = 0;
foreach ($accounts as $acc) {
    $totalBalance += (float)$acc['balance'];
}
?>

<h1>Mina Konton</h1>
<p class="page-description">Här ser du en översikt över alla dina konton och ditt totala saldo.</p>

<div class="card">
    <?php if (empty($accounts)): ?>
        <p class="empty-state">Du har inga konton ännu.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kontonummer</th>
                    <th>Kontotyp</th>
                    <th>Ränta</th>
                    <th class="cell-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $acc): ?>
                <tr>
                    <td class="text-bold">#<?= e($acc['id']) ?></td>
                    <td>
                        <span class="badge-label">
                            <?= e(match($acc['account_type']) {
                                'checking' => 'Lönekonto',
                                'savings'  => 'Sparkonto',
                                'fixed'    => 'Fasträntekonto',
                                'credit'   => 'Kreditkonto',
                                default    => $acc['account_type'],
                            }) ?>
                        </span>
                    </td>
                    <td class="text-muted">
                        <?= (isset($acc['interest_rate']) && $acc['interest_rate'] > 0) ? e($acc['interest_rate']) . ' %' : '—' ?>
                    </td>
                    <td class="cell-right text-bold">
                        <?= format_money((float)$acc['balance']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            
            <tfoot>
                <tr class="table-footer-row">
                    <td colspan="3" class="table-footer-label">Totalt belopp:</td>
                    <td class="table-footer-value"><?= format_money($totalBalance) ?></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>