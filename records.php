<?php
require_once 'app.php';
ensure_dirs();

$domain = $_GET['domain'] ?? '';
if ($domain === '') {
    header('Location: dns.php?error=Domain missing');
    exit;
}

$data = parse_zone_records($domain);
$records = $data['records'];
$statusMsg = '';
$statusClass = '';

if (isset($_GET['deleted'])) {
    $statusMsg = 'Record deleted.';
    $statusClass = 'success';
}
if (isset($_GET['saved'])) {
    $statusMsg = 'Record saved.';
    $statusClass = 'success';
}
if (isset($_GET['error'])) {
    $statusMsg = safe($_GET['error']);
    $statusClass = 'error';
}
if (isset($_GET['check'])) {
    $conf = run_cmd('sudo named-checkconf');
    $zones = run_cmd('sudo named-checkzones');
    $statusMsg = "named-checkconf:\n$conf\n---\nnamed-checkzones:\n$zones";
    $statusClass = 'neutral';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Records - <?php echo safe($domain); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <div class="card-header" style="margin-bottom: 12px;">
                <div>
                    <h1 class="page-title">Records for <?php echo safe($domain); ?></h1>
                    <p class="subtle">Add, edit, delete DNS records. Run checks after changes.</p>
                </div>
                <div class="inline-actions">
                    <button class="button" onclick="location.href='add_record.php?domain=<?php echo urlencode($domain); ?>'">Add Record</button>
                    <button class="button secondary" onclick="location.href='records.php?domain=<?php echo urlencode($domain); ?>&check=1'">Run Checks</button>
                    <button class="button secondary" onclick="location.href='dns.php'">Back to Domains</button>
                </div>
            </div>

            <?php if ($statusMsg): ?>
                <div class="alert <?php echo $statusClass; ?>">
                    <pre style="margin:0; white-space:pre-wrap; font-family:inherit;"><?php echo $statusMsg; ?></pre>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Records</h3>
                    <span class="badge">Total: <?php echo count($records); ?></span>
                </div>
                <?php if (!empty($records)): ?>
                    <table class="table">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>TTL</th>
                            <th>Value</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                        <?php foreach ($records as $idx => $rec): ?>
                            <tr>
                                <td><?php echo $idx + 1; ?></td>
                                <td><?php echo safe($rec['name']); ?></td>
                                <td><?php echo safe($rec['type']); ?></td>
                                <td><?php echo safe($rec['ttl']); ?></td>
                                <td><?php echo safe(trim(($rec['priority'] ? $rec['priority'] . ' ' : '') . $rec['value'])); ?></td>
                                <td>
                                    <div class="inline-actions">
                                        <button class="button secondary" onclick="location.href='edit_record.php?domain=<?php echo urlencode($domain); ?>&i=<?php echo $idx; ?>'">Edit</button>
                                        <button class="button danger" onclick="if(confirm('Delete record?')) location.href='delete_record.php?domain=<?php echo urlencode($domain); ?>&i=<?php echo $idx; ?>';">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p class="subtle">No records yet. Add one to begin.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
