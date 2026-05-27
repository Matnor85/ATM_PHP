<?php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,       
        'httponly' => true,        
        'samesite' => 'Strict',    
    ]);
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    session_start();
}

function require_atm_login(): void
{
    check_idle_timeout();

    if (empty($_SESSION['user_id']) || ($_SESSION['context'] ?? '') !== 'atm') {
        header('Location: /index.php?page=atm_login');
        exit;
    }
}

function require_admin_login(): void
{
    check_idle_timeout();

    if (empty($_SESSION['user_id']) || ($_SESSION['context'] ?? '') !== 'admin') {
        header('Location: /index.php?page=admin_login');
        exit;
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        require __DIR__ . '/../templates/403.php';
        exit;
    }
}

function require_login(): void
{
    check_idle_timeout();

    if (empty($_SESSION['user_id'])) {
        header('Location: /index.php?page=atm_login');
        exit;
    }
}

function require_role(string $role): void
{
    require_login();

    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        require __DIR__ . '/../templates/403.php';
        exit;
    }
}

function has_role(string $role): bool
{
    return ($_SESSION['role'] ?? '') === $role;
}

function check_idle_timeout(int $minutes = 30): void
{
    if (empty($_SESSION['user_id'])) {
        return;
    }

    $lastActive = $_SESSION['last_active'] ?? 0;

    if (time() - $lastActive > $minutes * 60) {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header('Location: /index.php?page=login&reason=timeout');
        exit;
    }

    $_SESSION['last_active'] = time();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8')
        . '">';
}

function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die('403 – Ogiltig CSRF-token.');
    }

    unset($_SESSION['csrf_token']);
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function format_money(float $amount): string
{
    return number_format($amount, 2, ',', ' ') . ' kr';
}

function format_date(string $datetime): string
{
    return date('Y-m-d H:i', strtotime($datetime));
}

function tx_type_label(string $type): string {
    return match($type) {
        'deposit' => 'Insättning',
        'withdrawal' => 'Uttag',
        'transfer' => 'Överföring',
        'bill_payment' => 'Räkning',
        default => $type
    };
}
