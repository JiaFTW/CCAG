<header>
    <div class="navbar">
    
        <div class="nav-left">
            <div class="title">CCAG</div>
            <a href="homepage.php">Homepage</a>
            <a href="searchPage.php">Recipes</a>
        <?php if (isset($_COOKIE['session_token'])) { ?>
            <a href="mppage.php">Make a Meal Plan</a>
            <a href="profilePage.php">Profile</a>
        <?php } ?>
        </div>

                    
        <div class="nav-right">    

        <?php if (isset($_COOKIE['session_token']) && isset($_COOKIE['username'])) {
    	    $username = htmlspecialchars($_COOKIE['username']); ?>
    	    <p class="navbar-text"><?= "Logged in: $username" ?></p>
    	    <a href="logout.php">Logout</a>
        <?php } else {?>
            <a href="loginPage.php">Login</a>
            <a href="registerPage.php">Register</a>
        </div>
                <?php } ?>
    </div>
</header>
