<?php 
    if (!isset($_SESSION)){
        session_start();
    }
?>

<header>
    <div class="navbar">
    
        <div class="nav-left">
            <div class="title">CCAG</div>
            <a href="homepage.php">Homepage</a>
            <a href="page1.php">Page 1</a>
            <a href="page2.php">Page 2</a>

        <?php if (isset($_SESSION['is_valid_user'])) { ?>
            <a href="secretpage.php">Secret Page</a> 
        <?php } ?>
        </div>

                    
        <div class="nav-right">    

        <?php if (isset($_SESSION['is_valid_user'])) { ?>
            <p class="navbar-text"><?php echo "Logged in: ", $_SESSION['user'];?></p>
            <a href="logout.php">Logout</a>
        <?php } else {?>
            <a href="loginPage.php">Login</a>
            <a href="registerPage.php">Register</a>
        </div>
                <?php } ?>
    </div>
</header>
