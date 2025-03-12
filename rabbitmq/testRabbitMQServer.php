#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php');

function doLogin($username,$password)
{
  
    $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

    return $connect->loginAccount($username, $password);
    
}

function doRegistration($username,$password,$email)
{
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->registerAccount($username, $email, $password);
}
function doValidate($token){

  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->validateSession($token);
  
}

function doLogout($token) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->invalidateSession($token);
}

function doDiet($username, $pref_array) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->changeUserPref($username, $pref_array);
}

function doGetDiet($username) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->getUserDiet($username);
}

function doAddFavorite($username, $rid) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->addFavorite($username, $rid);
}

function doRemoveFavorite($username, $rid) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->removeFavorite($username, $rid);
}

function doGetFavorites($username) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->getUserFavorites($username);
}

function doEditRecipe($rid, $ingredients, $name, $username) {
  $connect = new mysqlConnect('p:127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->editRecipe($rid, $ingredients, $name, $username);
}
function doAddReview($username, $rid, $rate, $text) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->addReview($username, $rid, $rate, $text);
}

function doRemoveReview($rate_id) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->removeReview($rate_id);
}

function doGetUserReviews($username) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->getUserReviews($username);
}

function doAddMealPlan($array) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->addMealPlan($username);

}

function doRecipe($keyword, $username) //perform search check
{
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  $labels = $connect->getUserDiet($username);

  print_r($labels);
 
  $response = $connect->checkRecipe($keyword); //TODO search by label

  if($response == 'false') { //return if mysql error
    echo "Recipe Search: Database ERROR | Returning DB_ERROR".PHP_EOL;
    return array('status' => 'DB_Error');
  }

  if ($response == null) { //will fetch from DMZ if no results from database
    
    echo "Recipe Search: Not Enough Results Found in Database | Calling DMZ".PHP_EOL;
    $client = new rabbitMQClient("testRabbitMQ.ini","DMZServer");
    $request = array();
    $request['type'] = "searchRecipe";
    $request['query'] = $keyword; 
    $dmz_response = $client->send_request($request);
    //print_r($dmz_response);
    if($dmz_response['status'] != 'success') {
      echo $dmz_response['message'];
      return array('status' => 'DMZ_Error');
    }
    if($connect->populateRecipe($dmz_response['recipes']) === false) {  //populate db with response
      echo "Recipe Search: Database Populating Issue | Returning DB_ERROR".PHP_EOL;
      return array('status' => 'DB_Error');
    }

    $response = $connect->checkRecipe($keyword); //perform search check agian */
  }

  echo "Recipe Search: Results Found in Database  | Returning Array".PHP_EOL;

  print_r($response);
  return $response;
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
    case "logout":
      return doLogout($request['sessionId']);
    case "register":
      return doRegistration($request['username'],$request['password'],$request['email']);
    case "getRecipe":
      return doRecipe($request['keyword'], $request['username']);
    case "editRecipe":
      return doEditRecipe($request['rid'], $request['ingredients'], $request['name'], $request['username']);
    case "diet":
      return doDiet($request['username'], $request['restrictions']);
    case "getDiet":
      return doGetDiet($request['username']);
    case "addFavorite":
      return doAddFavorite($request['username'], $request['rid']);
    case "removeFavorite":
      return doRemoveFavorite($request['username'], $request['rid']);
    case "getFavorites":
      return doGetFavorites($request['username']);
    case "addReview":
      return doAddReview($request['username'], $request['rid'], $request['rating'], $request['review']);
    case "removeReview":
      return doRemoveReview($request['rate_id']);
    case "getUserReviews":
      return doGetUserReviews($request['username']);
    case "addMealPlan":
      return doAddMealPlan($request);
    default:
      return "type fail".PHP_EOL;
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>




