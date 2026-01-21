<?php
// process_upload_site.php
// Handles creation of a site by calling scripts/create_site.sh with two arguments:
//   create_site.sh <site_name> <src_path>


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo "Method not allowed";
	exit;
}

$mode = $_POST['upload_mode'] ?? '';
$site = trim($_POST['site_name'] ?? '');
$src = trim($_POST['src_path'] ?? '');

$script = __DIR__ . '/scripts/create_site.sh';

if ($mode === 'path') {
	if ($site === '' || $src === '') {
		echo "Missing site name or source path.";
		exit;
	}

	// Try to resolve source path to an absolute path when possible
	$resolved = realpath($src);
	if ($resolved !== false) {
		$src = $resolved;
	}

	$cmd = 'sudo /bin/bash ' . escapeshellarg($script) . ' ' .
       escapeshellarg($site) . ' ' .
       escapeshellarg($src) . ' 2>&1';


	// Execute the script and capture output and return code
	exec($cmd, $output, $ret);

	// Redirect back to the upload form with a short status message.
	// Include exit code and a truncated output message (max 1000 chars).
	$code = intval($ret);
	$msg = substr(implode("\n", $output), 0, 1000);
	$msg = rawurlencode($msg);
	$status = ($code === 0) ? 'success' : 'error';

	header('Location: upload_site.php?status=' . $status . '&code=' . $code . '&msg=' . $msg);
	exit;
}

if ($mode === 'folder') {
	// Folder upload handler is intentionally minimal — inform user.
	echo "Folder upload processing is not implemented in this endpoint. Use 'Create Site From Server Path' instead.";
	exit;
}

echo "Unknown upload mode.";
exit;

