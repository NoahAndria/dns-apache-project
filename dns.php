<?php
require_once 'app.php';
ensure_dirs();

$domains = list_domains();
$statusMsg = '';
$statusClass = '';

if (isset($_GET['check'])) {
    $conf = run_cmd('sudo named-checkconf');
    $zones = run_cmd('sudo named-checkzones');
    $statusMsg = "named-checkconf:\n$conf\n---\nnamed-checkzones:\n$zones";
    $statusClass = 'neutral';
}

if (isset($_GET['deleted'])) {
    $statusMsg = 'Domain deleted.';
    $statusClass = 'success';
}
if (isset($_GET['error'])) {
    $statusMsg = safe($_GET['error']);
    $statusClass = 'error';
}
if (isset($_GET['added'])) {
    $statusMsg = 'Domain added.';
    $statusClass = 'success';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DNS Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <div class="card-header" style="margin-bottom: 12px;">
                <div>
                    <h1 class="page-title">DNS Domains</h1>
                    <p class="subtle">Manage zones, records, checks, and dig tests.</p>
                </div>
                <div class="inline-actions">
                    <button class="button" onclick="location.href='add_domain.php'">Add Domain</button>
                    <button class="button secondary" onclick="location.href='dns.php?check=1'">Run Checks</button>
                </div>
            </div>

            <?php if ($statusMsg): ?>
                <div class="alert <?php echo $statusClass; ?>">
                    <pre style="margin:0; white-space:pre-wrap; font-family:inherit;"><?php echo $statusMsg; ?></pre>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Existing Domains</h3>
                    <span class="badge">Total: <?php echo count($domains); ?></span>
                </div>
                <?php if (!empty($domains)): ?>
                    <table class="table">
                        <tr>
                            <th>Domain</th>
                            <th style="width: 280px;">Actions</th>
                        </tr>
                        <?php foreach ($domains as $domain): ?>
                            <tr>
                                <td><a href="records.php?domain=<?php echo urlencode($domain); ?>"><?php echo safe($domain); ?></a></td>
                                <td>
                                    <div class="inline-actions">
                                        <button class="button secondary" onclick="location.href='records.php?domain=<?php echo urlencode($domain); ?>'">Records</button>
                                        <button class="button secondary" onclick="location.href='dig.php?domain=<?php echo urlencode($domain); ?>'">Dig</button>
                                        <button class="button danger" onclick="if(confirm('Delete <?php echo safe($domain); ?>?')) location.href='delete_domain.php?domain=<?php echo urlencode($domain); ?>';">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p class="subtle">No domains found. Add one to get started.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
