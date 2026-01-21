<?php
require_once 'app.php';
ensure_dirs();

$domain = $_GET['domain'] ?? '';
if ($domain === '') {
    header('Location: dns.php?error=Domain missing');
    exit;
}

try {
    delete_domain_config($domain);
    header('Location: dns.php?deleted=1');
    exit;
} catch (Exception $e) {
    header('Location: dns.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>
