<?php
require_once 'app.php';

$action = $_GET['action'] ?? '';
$script = __DIR__ . '/scripts/update_bind.sh';

$cmds = [
    'start'   => 'sudo ' . $script . ' start',
    'stop'    => 'sudo ' . $script . ' stop',
    'restart' => 'sudo ' . $script . ' restart',
    'reload'  => 'sudo ' . $script . ' reload',
];


if (!isset($cmds[$action])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid action'
    ]);
    exit;
}

$output = [];
$returnCode = 0;

// Execute the command
exec($cmds[$action] . ' 2>&1', $output, $returnCode);

if ($returnCode !== 0) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'action'  => $action,
        'output'  => $output
    ]);
    exit;
}

// Success
echo json_encode([
    'success' => true,
    'action'  => $action,
    'output'  => $output
]);

header("Location: services.php");
?>
