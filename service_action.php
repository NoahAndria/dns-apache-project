<?php
require_once 'app.php';
$action = $_GET['action'] ?? '';
$cmds = [
    'start' => 'sudo systemctl start bind9',
    'stop' => 'sudo systemctl stop bind9',
    'restart' => 'sudo systemctl restart bind9',
];
if (!isset($cmds[$action])) {
    header('Location: services.php?result=' . urlencode('Unknown action'));
    exit;
}
$output = run_cmd($cmds[$action]);
header('Location: services.php?result=' . urlencode($action . ' -> ' . ($output === '' ? 'done (no output)' : $output)));
exit;
?>
