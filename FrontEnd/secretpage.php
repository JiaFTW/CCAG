<html>
<head>
    <title>CCAG Secret Page</title>
    <link rel="stylesheet" href="./styles/styles.css">
</head>
    <body>
    <?php include('header.php'); ?>
        <main>
            <p>Hey you found the secret page! <?php echo $_SESSION['user']?></p>
        </main>
    </body>
</html>