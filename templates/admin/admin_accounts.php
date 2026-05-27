<?php $pageTitle = 'Admin – Kundöversikt'; ?>
<?php require __DIR__ . '/admin_layout.php'; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-error"><?= e($_GET['error']) ?></div>
<?php endif; ?>
<?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success"><?= e($_GET['success']) ?></div>
<?php endif; ?>
<h1>Accounts Overview</h1>
<div class="card">
    <table>
        <thead>
            <tr>
                <th><a href="index.php?page=admin_accounts&sort=owner_name&order=<?= $nextOrder ?>" class="text-accent no-underline">Customer ▲▼</a></th>
                <th class="text-center"><a href="index.php?page=admin_accounts&sort=account_count&order=<?= $nextOrder ?>" class="text-accent no-underline">Account Count ▲▼</a></th>
                <th class="text-right"><a href="index.php?page=admin_accounts&sort=balance&order=<?= $nextOrder ?>" class="text-accent no-underline">Total Balance ▲▼</a></th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $cust): ?>
            <tr>
                <td><strong><?= e($cust['owner_name']) ?></strong></td>
                <td class="text-center">
                    <span class="badge badge-user"><?= e($cust['account_count']) ?> st</span>
                </td>
                <td class="text-right text-accent">
                    <strong><?= format_money((float)($cust['total_balance'] ?? 0)) ?></strong>
                </td>
                <td class="text-center">
                    <a href="index.php?page=admin_customer_details&id=<?= $cust['user_id'] ?>" class="btn-action text-accent no-underline">
                        [ Show Accounts ]
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layout_footer.php'; ?>