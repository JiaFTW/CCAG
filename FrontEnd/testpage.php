<?php
require_once('./logging/writelog.php');


if ($_SERVER['REQUEST_METHOD'] === "POST") {

writelog("SAYS HI", "bob"); 


}

?>


<!DOCTYPE html>
<html>
  <body>
    <h2>Testing</h2>
    <form method="POST"> 
      <button type="submit">BOB</button>
    </form>
  </body>
</html>