<?php
require_once 'app.php';
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
$rec = $records[$index];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Record - <?php echo safe($domain); ?></title>
    <link rel="stylesheet" href="style.css">
    <script>
        function togglePriority() {
            const type = document.getElementById('type').value;
            document.getElementById('priority-field').style.display = (type === 'MX') ? 'block' : 'none';
        }
    </script>
</head>
<body onload="togglePriority()">
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h1 class="page-title">Edit Record</h1>
                        <p class="subtle">Domain: <?php echo safe($domain); ?></p>
                    </div>
                </div>
                <form action="save_record.php" method="POST" class="form-grid">
                    <input type="hidden" name="domain" value="<?php echo safe($domain); ?>">
                    <input type="hidden" name="i" value="<?php echo $index; ?>">
                    <div>
                        <label class="label" for="name">Name / Host</label>
                        <input class="input" type="text" id="name" name="name" value="<?php echo safe($rec['name']); ?>" required>
                    </div>
                    <div>
                        <label class="label" for="ttl">TTL (seconds)</label>
                        <input class="input" type="number" id="ttl" name="ttl" value="<?php echo safe($rec['ttl']); ?>" min="0">
                    </div>
                    <div>
                        <label class="label" for="type">Record Type</label>
                        <select class="select" id="type" name="type" onchange="togglePriority()">
                            <?php foreach (['A','AAAA','CNAME','MX','NS','TXT'] as $t): ?>
                                <option value="<?php echo $t; ?>" <?php echo ($rec['type'] === $t ? 'selected' : ''); ?>><?php echo $t; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="priority-field" style="display:none;">
                        <label class="label" for="priority">Priority (MX)</label>
                        <input class="input" type="number" id="priority" name="priority" value="<?php echo safe($rec['priority']); ?>" min="0">
                    </div>
                    <div>
                        <label class="label" for="value">Value</label>
                        <input class="input" type="text" id="value" name="value" value="<?php echo safe($rec['value']); ?>" required>
                    </div>
                    <div class="inline-actions">
                        <button class="button" type="submit">Save</button>
                        <button class="button secondary" type="button" onclick="location.href='records.php?domain=<?php echo urlencode($domain); ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
