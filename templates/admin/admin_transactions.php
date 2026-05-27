<?php $pageTitle = 'Admin – Transaktioner'; ?>
<?php require __DIR__ . '/admin_layout.php'; ?>
<h1>Transaktioner</h1>
<div class="card">
    <form method="GET" action="index.php" class="filter-form">
        <input type="hidden" name="page" value="admin_transactions">
        <div>
            <label>Typ</label>
            <select name="type">
                <option value="">Alla typer</option>
                <option value="deposit"      <?= ($filterType ?? '') === 'deposit'      ? 'selected' : '' ?>>deposit</option>
                <option value="withdrawal"   <?= ($filterType ?? '') === 'withdrawal'   ? 'selected' : '' ?>>withdrawal</option>
                <option value="transfer"     <?= ($filterType ?? '') === 'transfer'     ? 'selected' : '' ?>>transfer</option>
                <option value="bill_payment" <?= ($filterType ?? '') === 'bill_payment' ? 'selected' : '' ?>>bill payment</option>
            </select>
        </div>
        <div>
            <label>Date from</label>
            <input type="date" name="date_from" value="<?= e($filterDateFrom ?? '') ?>">
        </div>
        <div>
            <label>Date to</label>
            <input type="date" name="date_to" value="<?= e($filterDateTo ?? '') ?>">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="index.php?page=admin_transactions" class="btn btn-secondary">Clear</a>
        </div>
    </form>
</div>
<div class="card">
    <div class="flex-between mb-md">
        <span class="text-sm text-muted"><?= $totalCount ?> transactions</span>
            <a href="index.php?page=admin_transactions&action=export_csv&<?= $qs ?>" class="btn btn-primary">
                ↓ Export CSV
            </a>
    </div>
    <?php if (empty($transactions)): ?>
        <p class="empty-state">Inga transaktioner matchar filtret.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Amount</th>
                <th>From Account</th>
                <th>To Account</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $tx): ?>
            <tr>
                <td class="text-muted"><?= e($tx['id']) ?></td>
                <td><span class="badge badge-<?= e($tx['type']) ?>"><?= e(tx_type_label($tx['type'])) ?></span></td>
                <td><strong><?= format_money((float)$tx['amount']) ?></strong></td>
                <td class="text-muted"><?= $tx['from_account_id'] ? '#' . e($tx['from_account_id']) : '—' ?></td>
                <td class="text-muted"><?= $tx['to_account_id']   ? '#' . e($tx['to_account_id'])   : '—' ?></td>
                <td class="text-muted"><?= format_date($tx['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=admin_transactions&p=1<?= $qs ?>"><<</a>
            <a href="?page=admin_transactions&p=<?= $currentPage - 1 ?><?= $filterType ? '&type=' . e($filterType) : '' ?>"><</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $currentPage): ?>
                <span class="current"><?= $i ?></span>
            <?php else: ?>
                <a href="?page=admin_transactions&p=<?= $i ?><?= $filterType ? '&type=' . e($filterType) : '' ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=admin_transactions&p=<?= $currentPage + 1 ?><?= $filterType ? '&type=' . e($filterType) : '' ?>">></a>
            <a href="?page=admin_transactions&p=<?= $totalPages ?><?= $filterType ? '&type=' . e($filterType) : '' ?>">>></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layout_footer.php'; ?>