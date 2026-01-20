<?php
require_once 'app.php';
$domain = $_GET['domain'] ?? '';
if ($domain === '') {
    header('Location: dns.php?error=Domain missing');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Record - <?php echo safe($domain); ?></title>
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
                        <h1 class="page-title">Add Record</h1>
                        <p class="subtle">Domain: <?php echo safe($domain); ?></p>
                    </div>
                </div>
                <form action="save_record.php" method="POST" class="form-grid">
                    <input type="hidden" name="domain" value="<?php echo safe($domain); ?>">
                    <div>
                        <label class="label" for="name">Name / Host</label>
                        <input class="input" type="text" id="name" name="name" placeholder="@, www, mail" required>
                    </div>
                    <div>
                        <label class="label" for="ttl">TTL (seconds)</label>
                        <input class="input" type="number" id="ttl" name="ttl" placeholder="3600" min="0">
                    </div>
                    <div>
                        <label class="label" for="type">Record Type</label>
                        <select class="select" id="type" name="type" onchange="togglePriority()">
                            <option value="A">A</option>
                            <option value="AAAA">AAAA</option>
                            <option value="CNAME">CNAME</option>
                            <option value="MX">MX</option>
                            <option value="NS">NS</option>
                            <option value="TXT">TXT</option>
                        </select>
                    </div>
                    <div id="priority-field" style="display:none;">
                        <label class="label" for="priority">Priority (MX)</label>
                        <input class="input" type="number" id="priority" name="priority" placeholder="10" min="0">
                    </div>
                    <div>
                        <label class="label" for="value">Value</label>
                        <input class="input" type="text" id="value" name="value" placeholder="Target (IP, host, text)" required>
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
