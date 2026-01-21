<?php require_once 'app.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Domain</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h1 class="page-title">Add New Domain</h1>
                        <p class="subtle">Provide a domain and its IP address.</p>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert success">Domain added successfully.</div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert error">Error: <?php echo safe($_GET['error']); ?></div>
                <?php endif; ?>

                <form action="process_add_domain.php" method="POST" class="form-grid">
                    <div>
                        <label class="label" for="domain">Domain</label>
                        <input class="input" type="text" id="domain" name="domain" placeholder="example.com" required>
                    </div>
                    <div>
                        <label class="label" for="ip_address">IP Address</label>
                        <input class="input" type="text" id="ip_address" name="ip_address" placeholder="192.168.1.10" required>
                    </div>
                    <div class="inline-actions">
                        <button class="button" type="submit">Validate & Add</button>
                        <button class="button secondary" type="button" onclick="location.href='dns.php'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
