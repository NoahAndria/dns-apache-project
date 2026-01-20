<?php
require_once 'app.php';
$domain = $_GET['domain'] ?? '';
if ($domain === '') {
    header('Location: dns.php?error=Domain missing');
    exit;
}
$out = run_cmd('dig ' . escapeshellarg($domain) . ' +short');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dig - <?php echo safe($domain); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h1 class="page-title">Dig Result</h1>
                    <p class="subtle">Domain: <?php echo safe($domain); ?></p>
                </div>
                <div class="alert neutral">
                    <pre style="margin:0; white-space:pre-wrap; font-family:inherit;">
<?php echo trim($out) === '' ? 'No output (command may require dig installed)' : safe($out); ?>
                    </pre>
                </div>
                <div class="inline-actions">
                    <button class="button secondary" onclick="location.href='dns.php'">Back</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
