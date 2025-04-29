<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_COOKIE['session_token'])) {
    header("Location: loginPage.php");
    exit();
}

require_once('../rabbitmq/testRabbitMQClient.php');

try {
    $statusResponse = sendMessage([
        'type' => 'get_2fa_status',
        'username' => $_COOKIE['username'],
        'message' => 'Status check'
    ]);
    
    // CORRECTED LINE BELOW
    if (!is_array($statusResponse)) {
        throw new Exception("Invalid server response format");
    }
    
    if ($statusResponse['status'] !== 'Success') {
        throw new Exception("2FA check failed: ".($statusResponse['message'] ?? ''));
    }
    
    $tfaEnabled = (bool)$statusResponse['tfa_enabled'];
    
} catch (Exception $e) {
    die("Error: ".htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Settings</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <?php include('header.php'); ?>
    <?php include('headerprofile.php'); ?>
</head>
<body>
    <div class="settings-container">
        <h2>Security Settings</h2>
        <form action="update2FA.php" method="POST">
            <label>
		<input type="checkbox" name="tfa_enabled" value="1" <?= $tfaEnabled ? 'checked' : '' ?>>
                Enable Two-Factor Authentication
            </label>
            <input type="submit" value="Save Settings">
        </form>
    </div>
</body>
</html>

