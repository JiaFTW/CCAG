<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('pulseCheck.php');

if(isset($argv[1]))
{
  $type = $argv[1];
}
else
{
  $type = "login";
}

if (isset($argv[2]))
{
  $msg = $argv[2];
}
else
{
  $msg = "test message";
}

function sendMessage($info){

if (detectCluster() === "Production_Backup") {
  if(pulseCheck($ccag_machines['Production_Main']['FrontEnd'])) {
    //echo "Main DMZ Server Online, ignoring request".PHP_EOL;
    return;
  }
}

$client = new rabbitMQClient("testRabbitMQ.ini", getRabbitMQChannel('backend'));
$request = array();
$request['type'] = $info['type'];
switch ($request['type'])
  {
    case "login":
      echo 'logging in';
      $request['username'] = $info['username'];
      $request['password'] = $info['password'];
      break;
    case "validate_session":
      $request['sessionId'] = $info['sessionId'];
      break;
    case "logout":
      $request['sessionId'] = $info['sessionId'];
      break;
    case "register":
      $request['username'] = $info['username'];
      $request['password'] = $info['password'];
      $request['email'] = $info['email'];
      break;
    case "getRecipe":
      $request['username'] = $info['username'];
      $request['keyword'] = $info['keyword'];
      break;
    case "diet":
      $request['username'] = $info['username'];
      $request['restrictions'] = $info['restrictions'];
      break;
    case "addFavorite":
      $request['username'] = $info['username'];
      $request['rid'] = $info['rid'];
      break;
    case "getFavorites":
      $request['username'] = $info['username'];
      break;
    case "getDiet":
      $request['username'] = $info['username'];
      break;
    case "removeFavorite":
      $request['username'] = $info['username'];
      $request['rid'] = $info['rid'];
      break;
    case "addReview":
      $request['username'] = $info['username'];
      $request['rid'] = $info['rid'];
      $request['rating'] = $info['rating'];
      $request['review'] = $info['review'];
      break;
    case "getUserReviews":
      $request['username'] = $info['username'];
      break;
    case "removeReview":
      $request['rate_id'] = $info['rate_id'];
      break;
    case "getRecs":
      $request['username'] = $info['username'];
      break;
    case "editRecipe":
      $request['rid'] = $info['rid'];
      $request['ingredients'] = $info['ingredients'];
      $request['name'] = $info['name'];
      $request['username'] = $info['username'];
      break;
    case "addMealPlan":
      $request['username'] = $info['username'];
      $request['MON1'] = $info['MON1'];
      $request['MON2'] = $info['MON2'];
      $request['MON3'] = $info['MON3'];
      $request['TUE1'] = $info['TUE1'];
      $request['TUE2'] = $info['TUE2'];
      $request['TUE3'] = $info['TUE3'];
      $request['WED1'] = $info['WED1'];
      $request['WED2'] = $info['WED2'];
      $request['WED3'] = $info['WED3'];
      $request['THU1'] = $info['THU1'];
      $request['THU1'] = $info['THU1'];
      $request['THU1'] = $info['THU1'];
      $request['FRI1'] = $info['FRI1'];
      $request['FRI2'] = $info['FRI2'];
      $request['FRI2'] = $info['FRI2'];
      $request['FRI3'] = $info['FRI3'];
      $request['SAT1'] = $info['SAT1'];
      $request['SAT2'] = $info['SAT2'];
      $request['SAT3'] = $info['SAT3'];
      $request['SUN1'] = $info['SUN1'];
      $request['SUN2'] = $info['SUN2'];
      $request['SUN3'] = $info['SUN3'];
      break;
    case "getUserMealPlans":
      $request['username'] = $info['username']; 
      break;
    case "getRex":
        $request['username'] = $info['username'];
      break;
    default:
      return $request['type'].PHP_EOL;
  }
$request['message'] = $info['message'] ?? 'No message provided';
$response = $client->send_request($request);
//$response = $client->publish($request);
return $response;
echo "client received response: ".PHP_EOL;
//print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;
}

?>

