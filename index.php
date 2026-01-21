<!DOCTYPE html>
<html>
<head>
    <title>DNS + APACHE Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <h1 class="page-title">DNS + APACHE Manager</h1>
            <p class="subtle">Choose which service you want to manage.</p>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); margin-top: 18px;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">DNS Management</h3>
                    </div>
                    <p class="subtle">Manage DNS domains, records, and checks.</p>
                    <button class="button" onclick="location.href='dns.php'">Open DNS</button>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">APACHE Management</h3>
                    </div>
                    <p class="subtle">Manage Apache sites (coming soon).</p>
                    <button class="button secondary" ><a href="upload_site.php">Upload webiste</a></button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
