<?php include('header.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>2FA Verification</title>
    <link rel="stylesheet" href="./styles/styles.css">
</head>
<body>
    <div class="verification-box">
        <h2>Two-Factor Authentication Required</h2>
        <?php if (isset($_GET['email'])): ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-alert"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <p>Enter the code sent to <?= htmlspecialchars($_GET['email']) ?></p>
            <form action="verify2FACode.php" method="POST">
                <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email']) ?>">
                <div class="form-group">
                    <label>Verification Code:</label>
                    <input type="text" name="code" required pattern="[a-f0-9]{32}">
                </div>
                <input type="submit" value="Verify" class="btn-primary">
            </form>
        <?php else: ?>
            <div class="error-alert">Invalid verification request</div>
        <?php endif; ?>
    </div>
</body>
</html>
