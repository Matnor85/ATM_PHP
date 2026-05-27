<?php
// Om routern hämtar sidan, eller om vi skickar med en header, rita INTE ut navigationen och head
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Låt PHP bara fortsätta till själva undersidan utan att rita ut nav och head!
    return;
}
$userId = $_SESSION['atm_user_id'];

$user = $userRepo->findById($userId);
$userName = $user['name'] ?? 'Användare';
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ATM/public/assets/css/atm_layout.css?v=<?= time() ?>">
    <title><?= e($pageTitle ?? 'Bankomat') ?></title>
</head>
<body>

<nav>
    <a href="index.php?page=dashboard" class="nav-brand">⬡ Bankomat</a>

    <div class="nav-links">
        <a href="index.php?page=dashboard"
           <?= (($page ?? '') === 'dashboard') ? 'class="active"' : '' ?>>Dashboard</a>
        <a href="index.php?page=withdraw"
           <?= (($page ?? '') === 'withdraw') ? 'class="active"' : '' ?>>Uttag</a>
        <a href="index.php?page=my_accounts"
           <?= (($page ?? '') === 'my_accounts') ? 'class="active"' : '' ?>>Konton</a>
        <a href="index.php?page=deposit"
           <?= (($page ?? '') === 'deposit') ? 'class="active"' : '' ?>>Insättning</a>
        <a href="index.php?page=transfer"
           <?= (($page ?? '') === 'transfer') ? 'class="active"' : '' ?>>Överföring</a>
        <a href="index.php?page=history"
           <?= (($page ?? '') === 'history') ? 'class="active"' : '' ?>>Historik</a>
        <a href="index.php?page=change_pin"
           <?= (($page ?? '') === 'change_pin') ? 'class="active"' : '' ?>>Byt PIN</a>
        <?php if (has_role('admin')): ?>
        <a href="index.php?page=admin_users"
           <?= str_starts_with(($page ?? ''), 'admin') ? 'class="active"' : '' ?>>⚙ Admin</a>
        <?php endif; ?>
    </div>

    <div class="nav-user">
        <strong><?= e($userName) ?></strong>
        <a href="index.php?action=logout" class="btn-logout">Logga ut</a>
    </div>
</nav>
<main>
        
<?php require __DIR__ . '/../layout_footer.php'; ?>