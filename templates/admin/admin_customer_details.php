<?php $pageTitle = 'Admin – Kunddetaljer'; ?>
<?php require __DIR__ . '/admin_layout.php'; ?>

<?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_GET['success']) ?>
    </div>
<?php endif; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>
<div class="mb-xl">
    <a href="index.php?page=admin_accounts" class="text-muted no-underline">← Tillbaka till kundöversikt</a>
</div>
<?php if ($customer): ?>
    <h1 class="mb-sm">Accounts for <?= e($customer['name']) ?></h1>
    <p class="text-muted mb-xl text-sm">
        User ID: #<?= e($customer['id']) ?> | Card Number: <?= e($customer['card_number']) ?>
    </p>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Account ID</th>
                    <th>Account Type</th>
                    <th class="text-right">Balance</th>
                    <th class="text-center">Interest Rate</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                    <th class="text-center">Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="5" class="empty-state">
                            The customer has no accounts.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($accounts as $acc): ?>
                    <tr>
                        <td class="text-muted"><?= e($acc['id']) ?></td>
                        <td>
                            <span class="badge badge-user">
                                <?= e(match($acc['account_type']) {
                                    'checking' => 'Checking Account',
                                    'savings'  => 'Savings Account',
                                    'fixed'    => 'Fixed Interest Account',
                                    'credit'   => 'Credit Account',
                                    default    => $acc['account_type'],
                                }) ?>
                            </span>
                        </td>
                        <td class="text-right text-accent">
                            <strong><?= format_money((float)$acc['balance']) ?></strong>
                        </td>
                        <td class="text-center">
                            <?php if ($acc['active']): ?>
                                <span class="status-active">● Active</span>
                            <?php else: ?>
                                <span class="status-frozen">● Frozen</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <form method="POST" action="index.php?page=admin_toggle_account" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="account_id"     value="<?= e($acc['id']) ?>">
                                <input type="hidden" name="user_id"        value="<?= e($customer['id']) ?>">
                                <input type="hidden" name="current_status" value="<?= e($acc['active']) ?>">
                                <button type="submit" class="btn-text">
                                    <?= $acc['active'] ? '[ Freeze ]' : '[ Activate ]' ?>
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            <form method="POST" action="index.php?page=admin_customer_details&id=<?= e($customer['id']) ?>" class="inline-form ml-md">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete_account">
                                <input type="hidden" name="account_id" value="<?= e($acc['id']) ?>">
                                <input type="hidden" name="user_id" value="<?= e($customer['id']) ?>">
                                <button type="submit" class="btn-text-danger" 
                                        onclick="return confirm('Är du säker på att du vill radera detta konto permanent?');">
                                    [ Delete ]
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3>Open New Account</h3>
        <form method="POST" action="index.php?page=admin_customer_details&id=<?= e($customer['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id"      value="<?= e($customer['id']) ?>">
            <input type="hidden" name="action" value="open_account">
            <div class="form-group">
                <label for="account_type">Account Type</label>
                <select id="account_type" name="account_type" required class="input-md">
                <option value="checking">Checking Account (0%)</option>
                <option value="savings">Savings Account (2.5%)</option>
                <option value="fixed">Fixed Interest Account (4.0%)</option>
                <option value="credit">Credit Account (0%)</option>
            </select>
            </div>
            <div class="form-group">
                <label for="insertCash">Start Balance (SEK)</label>
                <input type="number" id="insertCash" name="insertCash"
                       min="0" step="1" placeholder="0" class="input-md">
            </div>
            <button type="submit" class="btn btn-primary">Open Account</button>
        </form>
    </div>
    <div class="card">
        <h3>Transfer</h3>
        <form method="POST" action="index.php?page=admin_customer_details&id=<?= e($customer['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id"      value="<?= e($customer['id']) ?>">
            <input type="hidden" name="action" value="admin_transfer">
            <div class="form-group">
                <label for="from_account_id">From Account</label>
                <select id="from_account_id" name="from_account_id" required class="input-lg">
                    <option value="">-- Select Account --</option>
                    <?php foreach ($accounts as $acc): ?>
                        <?php if (!$acc['active']) continue; ?>
                        <option value="<?= e($acc['id']) ?>">
                            <?= e(match($acc['account_type']) { 
                                'checking'=>'Checking account', 
                                'savings'=>'Savings account', 
                                'fixed'=>'Fixed account', 
                                'credit'=>'Credit account', 
                                default=>$acc['account_type'] 
                                }) ?>
                            — <?= format_money((float)$acc['balance']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="to_account_internal">To Account (Own)</label>
                <select id="to_account_internal" name="to_account_id_internal" class="input-lg">
                    <option value="">-- Select Account --</option>
                    <?php foreach ($accounts as $acc): ?>
                        <option value="<?= e($acc['id']) ?>">
                            <?= e(match($acc['account_type']) {
                                'checking'=>'Checking account', 
                                'savings'=>'Savings account', 
                                'fixed'=>'Fixed account', 
                                'credit'=>'Credit account', 
                                default=>$acc['account_type'] 
                                }) ?>
                            — <?= format_money((float)$acc['balance']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="to_account_external">Or to another account ID</label>
                <input type="number" id="to_account_external" name="to_account_id_external"
                       placeholder="Recipient's account ID" class="input-lg">
                <small>Leave blank if you have selected an own account above.</small>
            </div>
            <div class="form-group">
                <label for="transfer_amount">Amount (SEK)</label>
                <input type="number" id="transfer_amount" name="amount"
                       step="1" min="1" required placeholder="0" class="input-lg">
            </div>
            <button type="submit" class="btn btn-primary">Accept Transfer</button>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-error">User not found.</div>
<?php endif; ?>
<?php require __DIR__ . '/../layout_footer.php'; ?>