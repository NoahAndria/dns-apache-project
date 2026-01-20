<?php
require_once 'app.php';
ensure_dirs();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dns.php');
    exit;
}

$domain = $_POST['domain'] ?? '';
$index = isset($_POST['i']) ? $_POST['i'] : '';
$name = trim($_POST['name'] ?? '');
$ttl = trim($_POST['ttl'] ?? '');
$type = strtoupper(trim($_POST['type'] ?? 'A'));
$value = trim($_POST['value'] ?? '');
$priority = trim($_POST['priority'] ?? '');

if ($domain === '' || $name === '' || $value === '') {
    header('Location: records.php?domain=' . urlencode($domain) . '&error=Missing required fields');
    exit;
}

if ($ttl !== '' && !ctype_digit($ttl)) {
    header('Location: records.php?domain=' . urlencode($domain) . '&error=Invalid TTL');
    exit;
}

$allowed = ['A','AAAA','CNAME','MX','NS','TXT'];
if (!in_array($type, $allowed, true)) {
    header('Location: records.php?domain=' . urlencode($domain) . '&error=Invalid type');
    exit;
}

$data = parse_zone_records($domain);
$records = $data['records'];

$newRecord = [
    'name' => $name,
    'type' => $type,
    'ttl' => $ttl,
    'value' => $value,
    'priority' => $type === 'MX' ? $priority : '',
];

if ($index === '' || $index === null || $index === false) {
    // add
    $records[] = $newRecord;
} else {
    $i = (int)$index;
    if (!isset($records[$i])) {
        header('Location: records.php?domain=' . urlencode($domain) . '&error=Record not found');
        exit;
    }
    $records[$i] = $newRecord;
}

write_zone_records($domain, $data['header'], $records);
header('Location: records.php?domain=' . urlencode($domain) . '&saved=1');
exit;
?>
