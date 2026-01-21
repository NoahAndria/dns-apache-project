<!DOCTYPE html>
<html>
<head>
    <title>Upload Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div style="margin-left: 220px; padding: 20px;">
        <h1>Upload Website</h1>
        
        <h3>Create Site From Server Path</h3>
        <p>Provide an existing server path where the site files already reside.</p>
        <form action="process_upload_site.php" method="post" style="max-width: 600px;">
            <input type="hidden" name="upload_mode" value="path" />
            <label for="site_name">Site Name:</label><br>
            <input type="text" id="site_name" name="site_name" placeholder="example.com" required style="width: 100%; padding: 8px; margin: 6px 0;" />
            <br>
            <label for="src_path">Source Path (on server):</label><br>
            <input type="text" id="src_path" name="src_path" placeholder="/path/to/site/source" required style="width: 100%; padding: 8px; margin: 6px 0;" />
            <br><br>
            <button type="submit" style="padding: 10px 16px;">Create Site from Path</button>
        </form>

        <h3><a href="apache_domains.php" style = "text-decoration: none"> >>>>>>>> Enable/Disable your websites <<<<<<<</a></h3>
    </div>
</body>
</html>
