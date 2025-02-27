<header>
    <div class="navbar">
        <a href="homepage.php">Homepage</a>
        <a href="page1.php">Page 1</a>
        <a href="page2.php">Page 2</a>
        <?php
            
            if (!isset($_SESSION)) {
                session_start();
                if (isset($_SESSION['is_valid_user'])) { ?>

                    <a href="secretpage.php">Secret Page</a> 
                    <a><?php echo "Logged in: ", $_SESSION['user'];?></a>
    
               <?php }
                else { ?>
                      
                    <a href="loginPage.php">Login</a>
        
                <?php}
            }?>
    </div>
</header>
