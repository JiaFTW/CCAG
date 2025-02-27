<header>
    <nav class="navbar">
        <div class="nav-left">
            <div class="title">CCAG</div>
            <a href="homepage.php">Homepage</a>
            <a href="page1.php">Page 1</a>
            <a href="page2.php">Page 2</a>
            <?php
                
                if (!isset($_SESSION)){
                    session_start();
                } 
                    if (isset($_SESSION['is_valid_user'])) { ?>

                        <a href="secretpage.php">Secret Page</a> 
        </div>

                    
        <div class="nav-right">

                    <p class="navbar-text"><?php echo "Logged in: ", $_SESSION['user'];?></p>

               <?php } else { ?> 
                    <a href="loginPage.php">Login</a>
                    <p>Hmmm</p>

        </div>
                <?php } ?>
    
    </nav>
</header>
