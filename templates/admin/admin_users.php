<?php $pageTitle = 'Admin – Användare'; ?>
    <?php require __DIR__ . '/admin_layout.php'; ?>
    <?php if (!empty($errorMsg)): ?>
    <div class="alert alert-error"><?= e($errorMsg) ?></div>
    <?php endif; ?>
  
    <div class="flex-between mb-lg">
        <h1 class="mb-0">Users</h1>
        <a href="index.php?page=admin_users&action=create" class="btn btn-primary">+ Create new User</a>
    </div>
    <?php if (($action ?? '') === 'create' || ($action ?? '') === 'edit'): ?>
    <div class="card">
        <h2><?= ($action === 'create') ? 'Create new User' : 'Edit User' ?></h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="index.php?page=admin_users">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="<?= e($action) ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="user_id" value="<?= e($editUser['id']) ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required
                    value="<?= e($editUser['name'] ?? $_POST['name'] ?? '') ?>">
            </div>
            <?php if ($action === 'edit'): ?>
            <div class="form-group">
                <label for="card_number">Card Number (16 digits)</label>
                <input type="text" id="card_number" name="card_number"
                    maxlength="16" minlength="16" required
                    value="<?= e($editUser['card_number'] ?? $_POST['card_number'] ?? '') ?>">
            </div>
            <?php endif; ?> 
            <?php if ($action === 'create'): ?>
                <div class="form-group">
                    <label for="account_type">Account Type (creates the first account automatically)</label>
                    <select id="account_type" name="account_type" required>
                        <option value="checking"  <?= ($_POST['account_type'] ?? '') === 'checking'  ? 'selected' : '' ?>>Checking Account</option>
                        <option value="savings"   <?= ($_POST['account_type'] ?? '') === 'savings'   ? 'selected' : '' ?>>Savings Account</option>
                        <option value="fixed" <?= ($_POST['account_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed Interest Account</option>
                        <option value="credit" <?= ($_POST['account_type'] ?? '') === 'credit' ? 'selected' : '' ?>>Credit Account</option>
                    </select>
                </div>   
                <div class="form-group">
                    <label for="pin">PIN Code</label>
                    <input type="password" id="pin" name="pin" required minlength="4" maxlength="10">
                </div>
                <div class="form-group">
                    <label for="initial_deposit">Initial Deposit (Amount to insert)</label>
                    <input type="number" step="0.01" id="initial_deposit" name="initial_deposit" value="0.00" required>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="user"  <?= (($editUser['role'] ?? 'user') === 'user')  ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= (($editUser['role'] ?? '')     === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <?= ($action === 'create') ? 'Create' : 'Save Changes' ?>
            </button>
            <a href="index.php?page=admin_users" class="btn btn-secondary ml-sm">Cancel</a>
        </form>
    </div>
    <?php endif; ?>
    <?php if (($action ?? '') === 'delete' && !empty($editUser)): ?>
    <div class="card card-danger-outline">
        <h2 class="text-danger">Remove User</h2>
        <p class="mb-md">
            Are you sure you want to remove <strong><?= e($editUser['name']) ?></strong>?
            All accounts and transactions associated with this user will also be deleted.
        </p>
        <form method="POST" action="index.php?page=admin_users">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete_confirm">
            <input type="hidden" name="user_id" value="<?= e($editUser['id']) ?>">
            <button type="submit" class="btn btn-danger">Yes, Remove</button>
            <a href="index.php?page=admin_users" class="btn btn-secondary ml-sm">Cancel</a>
        </form>
    </div>
    <?php endif; ?>
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success"><?= e($successMsg) ?></div>
    <?php endif; ?>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Card Number</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="text-muted"><?= e($user['id']) ?></td>
                    <td><strong><?= e($user['name']) ?></strong></td>
                    <td class="font-mono tracking-wide"><?= e($user['card_number']) ?></td>
                    <td><span class="badge badge-<?= e($user['role']) ?>"><?= e($user['role']) ?></span></td>
                    <td class="text-muted"><?= format_date($user['created_at']) ?></td>
                    <td>
                        <a href="index.php?page=admin_users&action=edit&user_id=<?= e($user['id']) ?>" class="text-accent no-underline mr-md">
                            Edit
                        </a>
                        <?php if ($user['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                        <a href="index.php?page=admin_users&action=delete&user_id=<?= e($user['id']) ?>" class="text-danger no-underline">
                            Delete
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php require __DIR__ . '/../layout_footer.php'; ?>