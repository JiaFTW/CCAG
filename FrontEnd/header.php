<header>
    <div class="navbar">
    
        <div class="nav-left">
            <div class="title">CCAG</div>
            <a href="homepage.php">Homepage</a>
            <a href="searchPage.php">Search</a>
        <?php if (isset($_COOKIE['session_token'])) { ?>
            <a href="profilePage.php">Profile</a>
        <?php } ?>
        </div>

                    
        <div class="nav-right">    

        <?php if (isset($_COOKIE['session_token'])) { ?>
            <p class="navbar-text"><?php echo "Logged in: ", $_COOKIE['username'];?></p>
            <a href="logout.php">Logout</a>
        <?php } else {?>
            <a href="loginPage.php">Login</a>
            <a href="registerPage.php">Register</a>
        </div>
                <?php } ?>
    </div>
</header>
