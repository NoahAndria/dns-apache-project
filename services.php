<?php
require_once 'app.php';
$statusMsg = '';
$statusClass = '';
if (isset($_GET['result'])) {
    $statusMsg = safe($_GET['result']);
    $statusClass = 'neutral';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Services</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h1 class="page-title">Services</h1>
                        <p class="subtle">Control bind9 service.</p>
                    </div>
                </div>
                <?php if ($statusMsg): ?>
                    <div class="alert neutral"><?php echo nl2br($statusMsg); ?></div>
                <?php endif; ?>
                <div class="inline-actions" style="margin-top:12px;">
                    <button class="button success" onclick="location.href='service_action.php?action=start'">Start</button>
                    <button class="button secondary" onclick="location.href='service_action.php?action=stop'">Stop</button>
                    <button class="button" onclick="location.href='service_action.php?action=restart'">Restart</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
