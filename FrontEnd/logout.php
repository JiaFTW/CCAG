<?php
//TEST CODE FOR $_SESSION ON CLIENT SIDE//
    session_start();
    $_SESSION = [];
    session_destroy();
//TEST CODE FOR $_SESSION ON CLIENT SIDE//

    header("Location: homepage.php")
?>