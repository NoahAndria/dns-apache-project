<?php
require_once 'app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_domain.php');
    exit;
}

$domain = trim($_POST['domain']);
$ip_address = trim($_POST['ip_address']);

if (empty($domain) || empty($ip_address)) {
    header('Location: add_domain.php?error=Domain and IP address are required');
    exit;
}

if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
    header('Location: add_domain.php?error=Invalid IP address format');
    exit;
}

if (!preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $domain)) {
    header('Location: add_domain.php?error=Invalid domain format');
    exit;
}

try {
    add_domain_config($domain, $ip_address);
    header('Location: dns.php?added=1');
    exit;
} catch (Exception $e) {
    header('Location: add_domain.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>
