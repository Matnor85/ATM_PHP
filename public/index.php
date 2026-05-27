<?php
session_start();

require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Repositories/UserRepository.php';
require_once __DIR__ . '/../src/Repositories/AccountRepository.php';
require_once __DIR__ . '/../src/Repositories/TransactionRepository.php';
require_once __DIR__ . '/../src/Repositories/AuditRepository.php';
require_once __DIR__ . '/../src/Services/TransactionService.php';
require_once __DIR__ . '/../src/Services/UserService.php';

$db = Database::connect();
// Repositories
$accountRepo = new AccountRepository($db);
$auditRepo = new AuditRepository($db);
$transactionRepo = new TransactionRepository($db);
$userRepo = new UserRepository($db, $accountRepo);
// Services
$transactionService = new TransactionService($db, $transactionRepo);
$userService = new UserService($db, $userRepo, $auditRepo);

$page = $_GET['page'] ?? 'home';

$action = $_GET['action'] ?? null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $action = $_POST['action'] ?? $_GET['action'] ?? null;

    switch ($action) {

        case 'change_pin_process':
           $userId = (int)($_SESSION['atm_user_id'] ?? 0);

           try {
            $result = $userService->changePin(
            $userId,
            $_POST['current_pin'] ?? '',
            $_POST['new_pin'] ?? '',
            $_POST['confirm_pin'] ?? ''
        );
        
        if (is_array($result) && isset($result['success'])) {

            if ($result['success']) {
                header("Location: index.php?page=change_pin&success=" . urlencode($result['message']));
            } else {
                header("Location: index.php?page=change_pin&error=" . urlencode($result['message']));
            }
        } else {
            header("Location: index.php?page=change_pin&success=PIN-koden har ändrats!");
        }

        } catch (Exception $e) {
            header("Location: index.php?page=change_pin&error=" . urlencode($e->getMessage()));
        }

        exit;

        case 'install_database':
                try {
                    $sql = file_get_contents(__DIR__ . '/../database/bankomat.sql');

                    $db->exec($sql);

                    echo "<script>
                            alert('SERVER HACKAD: Databasen har återställts och admin-användare är skapad!');
                            window.location.href = 'index.php?page=admin_login';
                          </script>";
                    exit;
                } catch (Exception $e) {
                    die("Misslyckades att installera databasen: " . $e->getMessage());
                    }

        case 'atm_login_process':

            $user = $userRepo->findByCardNumber($_POST['card_number'] ?? '');
            if ($user && password_verify($_POST['pin'] ?? '', $user['pin_hash'])) {
                $_SESSION['atm_logged_in'] = true;
                $_SESSION['atm_user_id'] = $user['id'];
                header("Location: index.php?page=dashboard");
            } else {
                header("Location: index.php?page=home&error=Felaktig PIN.");
            }
            exit;

        case 'admin_login_process':

            $user = $userRepo->findByCardNumber($_POST['card_number'] ?? '');

            if ($user && password_verify($_POST['pin'] ?? '', $user['pin_hash']) && $user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_id'] = $user['id'];

                $auditRepo->log($user['id'], 'admin_login_success', 'Admin loggade in');
                header("Location: index.php?page=admin_users");
            } else {

                $auditRepo->log(null, 'admin_login_failed', 'Misslyckat inloggningsförsök för kort: ' . ($_POST['card_number'] ?? 'n/a'));
                header("Location: index.php?page=admin_login&error=Nekad åtkomst.");
            }
            exit;

        case 'create':

            if (empty($_SESSION['admin_logged_in'])) { die('Åtkomst nekad'); }
            $userService->create(
                $_POST['name'],
                $_POST['pin'],
                $_POST['account_type'],
                $_POST['role'],
                (float)($_POST['initial_deposit'] ?? 0) 
            );
            header("Location: index.php?page=admin_users&success=Användare och konto skapat");
            exit;

        case 'edit':

            if (empty($_SESSION['admin_logged_in'])) { die('Åtkomst nekad'); }
            $userService->update((int)$_POST['user_id'], $_POST['name'], $_POST['card_number'], $_POST['role']);
            header("Location: index.php?page=admin_users&success=Uppdaterad");
            exit;

       case 'delete_confirm':

        if (empty($_SESSION['admin_logged_in'])) { die('Åtkomst nekad'); }
             try {
                 $userService->delete((int)$_POST['user_id']);
                 header("Location: index.php?page=admin_users&success=Användare borttagen");
             } catch (Exception $e) {
                 header("Location: index.php?page=admin_users&error=" . urlencode($e->getMessage()));
             }
             exit;  

        case 'open_account':

            if (empty($_SESSION['admin_logged_in'])) { die('Åtkomst nekad'); }
            $accountRepo->createNewAccount((int)$_POST['user_id'], $_POST['account_type'], (float)$_POST['insertCash']);
            header("Location: index.php?page=admin_customer_details&id=" . $_POST['user_id'] . "&success=Konto öppnat");
            exit;

        case 'admin_transfer':

            if (empty($_SESSION['admin_logged_in'])) { die('Åtkomst nekad'); }
            $result = $transactionService->executeTransfer(
                (int)$_POST['from_account_id'],
                (int)$_POST['to_account_id_external'] ?: (int)$_POST['to_account_id_internal'],
                (float)$_POST['amount'],
                (int)$_POST['user_id']
            );
            $msg = $result['success'] ? "success=" . urlencode($result['message']) : "error=" . urlencode($result['message']);
            header("Location: index.php?page=admin_customer_details&id=" . $_POST['user_id'] . "&$msg");
            exit;

        case 'delete_account':

            if (empty($_SESSION['admin_logged_in'])) { die('Åtkomst nekad'); }
            try {
                $accountRepo->deleteAccount((int)$_POST['account_id']);
                header("Location: index.php?page=admin_customer_details&id=" . $_POST['user_id'] . "&success=Konto raderat");
            } catch (Exception $e) {
                header("Location: index.php?page=admin_customer_details&id=" . $_POST['user_id'] . "&error=" . urlencode($e->getMessage()));
            }
            exit;
            }
}

if ($page === 'logout') {
    $isAdmin = isset($_SESSION['admin_logged_in']);
    $auditRepo->log($_SESSION['user_id'] ?? null, 'admin_logout', 'Användare loggade ut');
    $_SESSION = [];
    session_destroy();
   
    if ($isAdmin) {
        header("Location: index.php?page=admin_login");
    } else {
        header("Location: index.php?page=home");
    }
    exit;
}

$public_pages = [
    'home',
    'admin_login',
    'around-the-corner-closed-door',
    'around-the-corner-open-door',
    'camera',
    'real-atm-nr1'
    ];

if (str_starts_with($page, 'admin_') && $page !== 'admin_login' && empty($_SESSION['admin_logged_in'])) {
    header("Location: index.php?page=admin_login&error=Logga in som admin.");
    exit;
}

$admin_pages = [
    'admin_accounts',
    'admin_audit_log',
    'admin_customer_details',
    'admin_settings',
    'admin_transactions',
    'admin_users'
    ];

if (in_array($page, $public_pages)) {

if ($page === 'home') {
        require_once __DIR__ . '/../templates/home.php';

        } elseif (str_contains($page, 'atm')) {
        require_once __DIR__ . '/../templates/atm/' . $page . '.php';

        } elseif ($page === 'admin_login') {
        require_once __DIR__ . '/../templates/admin/admin_login.php';

        } else {
        require_once __DIR__ . '/../templates/' . $page . '.php';
    }
    if ($page === 'real-atm-nr1') {
    $currentPage = (int)($_GET['p'] ?? 1);
    $limit = 5;
    $users = $userRepo->findAllUsersOnly($currentPage, $limit);
   
    $totalUsers = $userRepo->countTotalUsers();
    $totalPages = ceil($totalUsers / $limit);
   
    require_once __DIR__ . '/../templates/atm/real-atm-nr1.php';

    exit;
}
    exit;
}

if (in_array($page, ['dashboard', 'withdraw', 'my_accounts', 'deposit', 'transfer', 'history', 'change_pin'])) {
    if (empty($_SESSION['atm_logged_in'])) {
        header("Location: index.php?page=home&error=Logga in först.");
        exit;
    }
    require_once __DIR__ . '/../templates/atm/atm_layout.php';
    require_once __DIR__ . '/../templates/atm/' . $page . '.php';
    require_once __DIR__ . '/../templates/layout_footer.php';
    exit;

}


if (in_array($page, $admin_pages)) {
    if (empty($_SESSION['admin_logged_in'])) {
        header("Location: index.php?page=admin_login&error=Nekad tillgång.");
        exit;
        }
       
        if ($page === 'admin_transactions' && ($action ?? '') === 'export_csv') {
        $transactions = $transactionRepo->findFilteredNoLimit(
            $_GET['type'] ?? null,
            $_GET['date_from'] ?? null,
            $_GET['date_to'] ?? null
        );
   
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="transaktioner_' . date('Y-m-d') . '.csv"');
   
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Type', 'Amount', 'From', 'To', 'Date']);
   
        foreach ($transactions as $t) {
            fputcsv($output, [$t['id'], $t['type'], $t['amount'], $t['from_account'], $t['to_account'], $t['date']]);
        }
       
        fclose($output);
        exit;
    }

    if ($page === 'admin_audit_log' && ($action ?? '') === 'export_csv') {
        $logs = $auditRepo->findAllNoLimit(); 

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit_log_' . date('Y-m-d') . '.csv"');
           
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'User ID', 'Action', 'Details', 'Created At']);
           
        foreach ($logs as $log) {
            fputcsv($output, [$log['id'], $log['user_id'], $log['action'], $log['details'], $log['created_at']]);
        }
        fclose($output);
        exit;
    }

    if ($page === 'admin_accounts') {
        $customers = $accountRepo->getCustomersOverview();
    }
   
    if ($page === 'admin_users') {
    $users = $userRepo->findAll();
   
    $action = $_GET['action'] ?? null;
    $userId = (int)($_GET['user_id'] ?? 0);
   
    if (($action === 'edit' || $action === 'delete') && $userId > 0) {
        $editUser = $userRepo->findById($userId);
    }
   
    $successMsg = $_GET['success'] ?? null;
    $errorMsg = $_GET['error'] ?? null;
    }
   
    if ($page === 'admin_customer_details') {
        $customer = $userRepo->findById((int)($_GET['id'] ?? 0));
        $accounts = $accountRepo->findByUserId((int)($_GET['id'] ?? 0));
    }

    if ($page === 'admin_transactions') {
        $filterType = $_GET['type'] ?? null;
        $filterDateFrom = $_GET['date_from'] ?? null;
        $filterDateTo = $_GET['date_to'] ?? null;
        $currentPage = (int)($_GET['p'] ?? 1);
        $limit = 20;
        $offset = ($currentPage - 1) * $limit;

        $transactions = $transactionRepo->findAll($filterType, $filterDateFrom, $filterDateTo, $limit, $offset);
        $totalCount = $transactionRepo->countAll($filterType, $filterDateFrom, $filterDateTo);
        $totalPages = ceil($totalCount / $limit);
       
        $qs = http_build_query(array_filter(['type' => $filterType, 'date_from' => $filterDateFrom, 'date_to' => $filterDateTo]));
    }

    if ($page === 'admin_audit_log') {
        $filterType = $_GET['type'] ?? '';
        $filterDateFrom = $_GET['date_from'] ?? '';
        $filterDateTo = $_GET['date_to'] ?? '';
        $currentPage = (int)($_GET['p'] ?? 1);
        $limit = 20;
        $offset = ($currentPage - 1) * $limit;

        if (!empty($filterType) || !empty($filterDateFrom) || !empty($filterDateTo)) {
            $logs = $auditRepo->findFiltered($filterType, $filterDateFrom, $filterDateTo, $limit, $offset);
            $totalCount = $auditRepo->countFiltered($filterType, $filterDateFrom, $filterDateTo);
        } else {
            $logs = $auditRepo->findAll($limit, $offset);
            $totalCount = $auditRepo->countAll();
        }
       
        $totalPages = ceil($totalCount / $limit);
    }
  
    require_once __DIR__ . '/../templates/admin/' . $page . '.php';
    exit;
}

require_once __DIR__ . '/../templates/403.php';

exit; 