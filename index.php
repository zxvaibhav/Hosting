<?php
$storage_file = __DIR__ . '/device_verifications.json';

// Ensure file exists
if (!file_exists($storage_file)) {
    file_put_contents($storage_file, json_encode([]));
    chmod($storage_file, 0666);
}

// Check if file is writable
if (!is_writable($storage_file)) {
    $error = 'Cannot write to storage file. Check permissions.';
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $error]);
    exit;
}

// Get parameters from request
$device_id = isset($_GET['device_id']) ? trim($_GET['device_id']) : null;
$mobile_name = isset($_GET['mobile_name']) ? trim($_GET['mobile_name']) : 'Unknown';
$model = isset($_GET['model']) ? trim($_GET['model']) : 'Unknown';
$app_name = isset($_GET['app_name']) ? trim($_GET['app_name']) : 'Unknown';
$package_name = isset($_GET['package_name']) ? trim($_GET['package_name']) : 'Unknown';
$app_version = isset($_GET['app_version']) ? trim($_GET['app_version']) : 'Unknown';
$telegram_link = isset($_GET['telegram_link']) ? trim($_GET['telegram_link']) : '';

// Validate device ID and app_name
if (!$device_id || !$app_name) {
    $error = 'Device ID or App Name is missing.';
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $error]);
    exit;
}

// Load existing data
$data = file_exists($storage_file) ? json_decode(file_get_contents($storage_file), true) : [];

// Initialize app-specific data if not exists
if (!isset($data[$app_name])) {
    $data[$app_name] = [];
}

// Save device ID and metadata if not exists
if (!isset($data[$app_name][$device_id])) {
    $created_at = date('Y-m-d H:i:s');
    $expiry_time = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $data[$app_name][$device_id] = [
        'created_at' => $created_at,
        'expiry_time' => $expiry_time,
        'mobile_name' => $mobile_name,
        'model' => $model,
        'app_name' => $app_name,
        'package_name' => $package_name,
        'app_version' => $app_version,
        'telegram_link' => $telegram_link
    ];

    if (!file_put_contents($storage_file, json_encode($data, JSON_PRETTY_PRINT))) {
        $error = 'Failed to save device ID to file.';
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $error]);
        exit;
    }
}

// Redirect to verify_success.php
$redirect_url = "https://badmodx.xyz/" . basename(__DIR__) . "/verify_success.php?device_id=" . urlencode($device_id) .
                "&mobile_name=" . urlencode($mobile_name) .
                "&model=" . urlencode($model) .
                "&app_name=" . urlencode($app_name) .
                "&package_name=" . urlencode($package_name) .
                "&app_version=" . urlencode($app_version) .
                "&telegram_link=" . urlencode($telegram_link);
header("Location: $redirect_url");
exit;
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>लिंक शॉर्टनर - AnsariBad</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; background: #f0f0f0; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bad Boy Ads</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>