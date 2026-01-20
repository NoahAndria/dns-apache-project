<?php
require_once 'app.php';
ensure_dirs();

$domain = $_GET['domain'] ?? '';
$index = isset($_GET['i']) ? (int)$_GET['i'] : -1;
if ($domain === '' || $index < 0) {
    header('Location: dns.php?error=Missing params');
    exit;
}

$data = parse_zone_records($domain);
$records = $data['records'];
if (!isset($records[$index])) {
    header('Location: records.php?domain=' . urlencode($domain) . '&error=Record not found');
    exit;
}
unset($records[$index]);
$records = array_values($records);

write_zone_records($domain, $data['header'], $records);
header('Location: records.php?domain=' . urlencode($domain) . '&deleted=1');
exit;
?>
