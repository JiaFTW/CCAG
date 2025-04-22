<?php include('header.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <link rel="stylesheet" href="./styles/styles.css">
</head>
<body>
    <div class="verification-box">
        <h2>Email Verification Required</h2>
        <?php if (isset($_GET['email'])): ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-alert">
                    <?php
                    $errors = [
                        'system_error' => 'System error. Please try again.',
                        'invalid_code' => 'Invalid verification code',
                        'code_expired' => 'Code has expired',
                        'invalid_input' => 'Invalid input format'
                    ];
                    echo $errors[$_GET['error']] ?? 'Unknown error occurred';
                    ?>
                </div>
            <?php endif; ?>
            <p>We sent a verification code to <?php echo htmlspecialchars($_GET['email']); ?></p>
            <form action="verifyCode.php" method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
                <div class="form-group">
                    <label>Verification Code:</label>
                    <input type="text" name="code" required pattern="[a-f0-9]{32}">
                </div>
                <input type="submit" value="Verify" class="btn-primary">
            </form>
            <p class="resend-link">Didn't receive code? 
                <a href="resendCode.php?email=<?php echo urlencode($_GET['email']); ?>">Resend</a>
            </p>
        <?php else: ?>
            <div class="error-alert">Invalid verification request</div>
        <?php endif; ?>
    </div>
</body>
</html>
