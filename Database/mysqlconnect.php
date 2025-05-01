#!/usr/bin/php
<?php

//TODO: make this into class or function

require_once 'dbFunctionsLib.php';

class mysqlConnect {
	protected $dbConnectionStatus = false; 
	protected $mydb;

	public function __construct($address, $db_user, $db_pass, $db_name) {
		//use 127.0.0.1 to connect to your local mysql server.
		$this->mydb = new mysqli($address,$db_user ,$db_pass, $db_name);
		$this->connectDB();
	}

	protected function connectDB() {
		if ($this->mydb->errno != 0) {
			echo __CLASS__." failed to connect to database: ". $this->mydb->error . PHP_EOL;
			$dbConnectionStatus = false;
		}
		else {
			echo __CLASS__." successfully connected to database".PHP_EOL;
			$dbConnectionStatus = true;
		}
	}

	//Returns Bool
	public function getConnectionStatus () {
		return $this->dbConnectionStatus;
	}

//BackEnd Functions

	//Returns Array
	public function registerAccount($username, $email, $password) {
		$register_status;
		$invalid_status = isDuplicateFound($username, "username", "accounts", $this->mydb) ? 'user_duplicate' : '';
		$invalid_status = isDuplicateFound($email, "email", "accounts", $this->mydb) ? 'email_duplicate' : '';

		if ($invalid_status != '') {
			$register_status = 'Invalid';
			return array('status' => $register_status, 'invalid_type' => $invalid_status);
		}
		//TODO: Validate Email and user format
		//TODO: Hash Password before query

		$register_status = addAccount($username, $email, $password, $this->mydb) ? 'Success' : 'Error';


		return array('status' => $register_status, 'invalid_type' => null);
	}

	//Returns Array
	public function loginAccount($username, $password) {
		echo 'sdioj';
		$query = "SELECT username, password FROM accounts 
		WHERE username = '".$username."';";
		$status;
		$cookie = null;
	
		$response = handleQuery($query, $this->mydb, "Query Status: Login Succesfull");
	
		if ($response == false) {
			$status = 'Error';
			return array('status' => $status, 'cookie' => $cookie );
		}

		$ac = $response->fetch_assoc();

		if ($ac == null || $password != $ac['password']) { //TODO: change != to password_verify() or bycrypt_vertify() for hashed
			$status = 'Invalid';
		} 
		else {
			$status = 'Success';
			$cookie = generateSession($username, 3600, $this->mydb);
		}

		$arraytest = array('status' => $status, 'cookie' => $cookie, 'username' => $username );
		//showAr($arraytest);
		return $arraytest; 
	
	}


	//Returns Array
	public function validateSession($token) {
		$status;
		$query = "SELECT cookie_token, end_time FROM sessions 
		WHERE cookie_token = '".$token."';";    //TODO change to verftiy() for hashed tokens (might need to change query)
		$response = handleQuery($query, $this->mydb, "Query Status: Validate Session Succesfull");
		if ($response == false) {
			$status = 'Error';
			return array('status' => $status);
		}

		$response_arr = $response->fetch_assoc();
		
		if($response_arr == null) {
			$status = 'NotFound';
			return array('status' => $status);
		}
		elseif ($response_arr['end_time'] <= time()) {
			$status = 'Expired';
			return array('status' => $status);
		} 
		else {
			$status = 'Success';
			return array('status' => $status);
		}
	}

	public function invalidateSession($token) {
		$query = "DELETE FROM sessions WHERE cookie_token = '".$token."';";
		$response = handleQuery($query, $this->mydb, "Query Status: Invalidate Session Successfull");
		
		return array('status' => $response ? 'Success' : 'Error');
	}
	
	//wrappers
	public function getUserDiet(string $username) { //wrapper function for getUserPref()
		return getUserPref($username, $this->mydb);
	}

	public function getUserFavorites(string $username) { //wrapper funciton for getUserBookmarks()
		return getUserBookmarks($username, $this->mydb);
	}

	public function getUserReviews(string $username) { //wrapper function for getReviewsByUser()
		return getReviewsByUser($username, $this->mydb);
	}	

	public function getUserMealPlans(string $username) { //wrapper fucntion for getUserMP()
		return getUserMP($username, $this->mydb);
	}
 
	//recipes
	public function checkRecipe($keywords, $labels = '') {
		
		$formatted_labels = ''; //Declaring Strings
		$formatted_search = '';
		$label_query = '';

		$serch_arr = array_filter(array_map('trim', explode(' ', $keywords)));
		$formatted_search = array_map(function($keyword) {return '+' . $keyword . '*'; }, $serch_arr); //Reformating to be compatible with query

		
    	if ($labels != '' && $labels != null) {
			echo "Check Recipe: Label Filter Enabled".PHP_EOL;
			$formatted_labels = array_map(function($label) { return "'" . $label . "'"; }, $labels);
 
        	$label_query = "INNER JOIN (
            SELECT rl.rid 
            FROM recipe_labels rl
            INNER JOIN labels l ON rl.label_id = l.label_id
            WHERE l.label_name IN (" . implode(',', $formatted_labels) . ")
            GROUP BY rl.rid
            HAVING COUNT(DISTINCT l.label_name) = " . count($labels) . ") AS filtered_rids ON recipes.rid = filtered_rids.rid";
    	}

		$query = "SELECT recipes.rid, recipes.name, recipes.image, recipes.num_ingredients, recipes.ingredients, recipes.calories, recipes.servings, GROUP_CONCAT(DISTINCT labels.label_name SEPARATOR ', ') AS labels_str
		FROM recipes INNER JOIN recipe_labels  ON recipes.rid = recipe_labels.rid INNER JOIN labels ON recipe_labels.label_id = labels.label_id
		".$label_query." WHERE MATCH(recipes.name) AGAINST ('+".$keywords."*' IN BOOLEAN MODE) 
		GROUP BY recipes.rid;";

		$response = handleQuery($query, $this->mydb, "Query Status: Check Recipe Successfull");
		if ($response === false) {
			echo "Check Recipe Status: ERROR!! | Returning String: false".PHP_EOL;
			return 'false';
		}

		$response_arr = $response->fetch_all(MYSQLI_ASSOC); 

		if ($response_arr == null) {
			echo "Check Recipe Status: NULL | Returning NULL";
			return null;
		}

		echo "Check Recipe Status: Success| Returning Results";
		//print_r($response_arr);
		return $response_arr;
	}

	public function checkRecipeByLabel(string $labels_str = '', int $limit = 5) {
		$labels = array_filter(array_map('trim', explode(',', $labels_str)));
		$label_query = '';
	
		if (!empty($labels)) {
			$formatted_labels = array_map(function($label) { return "'" . $label . "'";}, $labels);
	
			$label_query = "INNER JOIN (
				SELECT rl.rid 
				FROM recipe_labels rl
				INNER JOIN labels l ON rl.label_id = l.label_id
				WHERE l.label_name IN (" . implode(',', $formatted_labels) . ")
				GROUP BY rl.rid
				HAVING COUNT(DISTINCT l.label_name) = " . count($labels) . "
			) AS filtered_rids ON recipes.rid = filtered_rids.rid";
		}
	
		$query = "SELECT recipes.rid, recipes.name, recipes.image, 
						 recipes.num_ingredients, recipes.ingredients, 
						 recipes.calories, recipes.servings,
						 GROUP_CONCAT(DISTINCT labels.label_name SEPARATOR ', ') AS labels_str
				  FROM recipes
				  INNER JOIN recipe_labels ON recipes.rid = recipe_labels.rid
				  INNER JOIN labels ON recipe_labels.label_id = labels.label_id
				  $label_query
				  GROUP BY recipes.rid
				  LIMIT $limit";
	
		$response = handleQuery($query, $this->mydb, "Query Status: Check Recipe By Label Successfull");
		if ($response === false) {
			echo "Check Recipe Status: ERROR!! | Returning false".PHP_EOL;
			return false;
		}
		$response_arr = $response->fetch_all(MYSQLI_ASSOC);

		if ($response_arr == null) {
			echo "Check Recipe Status: NULL | Returning NULL";
			return null;
		}
		echo "Check Recipe Status: Success| Returning Results";
		//print_r($response_arr);
		return $response_arr;
	}

	public function populateRecipe($array) {
		$fail_counter = 0;
		$total = count($array);

		if ($total == 0) {
			return false;
		}

		foreach($array as $row) {
			$success = addRecipe($row['name'],
            $row['image'],
            $row['num_ingredients'],
            $row['ingredients'],
            $row['calories'],
            $row['servings'],
            $row['labels'], //long string
			$this->mydb);

			if(!$success) {
				$fail_counter++;
			}
		}
		echo $fail_counter;
		return $fail_counter < $total;

	}


	//user funcitons
	public function changeUserPref(string $username, $pref_array) {
		$uid = getUIDbyUsername($username, $this->mydb);
		if($uid === null) {
			return array('status' => 'Error');
		}
		$formatted_labels = "'" . implode("','", $pref_array) . "'";

		$checkNumQuery = "SELECT COUNT(uid) AS total FROM user_pref WHERE uid = ".$uid.";" ;
		$response = handleQuery($checkNumQuery, $this->mydb, "Query Status: User Pref Count Successfull");
		$sum_arr = $response->fetch_assoc();
		if ($sum_arr['total'] > 0) {
			$delete_query = "DELETE FROM user_pref WHERE uid = ".$uid.";" ;
			$response = handleQuery($delete_query, $this->mydb, "Query Status: User Pref Delete Successfull");
			if ($response === false) {
				return array('status' => 'Error');
			}
		}

		$query = "INSERT INTO user_pref (uid, label_id)
		SELECT ".$uid.", label_id
		FROM labels WHERE label_name IN (".$formatted_labels.");";
		$response = handleQuery($query, $this->mydb, "Query Status: Add User: ".$username." Prefs Successfull");

		//$debug = getUserPref($username, $this->mydb);
		//print_r($debug);
		//return array('status' => $response ? 'Success' : 'Error');
		return getUserPref($username, $this->mydb);
	}

	public function addFavorite($username, $rid) {
		$uid = getUIDbyUsername($username, $this->mydb);
		if($uid === null || !is_int($rid)) {
			return array('status' => 'Error');
		}
		if(isTwoDuplicatesFound($uid, $rid, 'uid', 'rid', 'bookmarks', $this->mydb)) {
			return array('status' => 'Error_Duplicate');
		}

		$query = "INSERT INTO bookmarks VALUES (".$uid.", ".$rid.");";
		$response = handleQuery($query, $this->mydb, "Query Status: Add Favorite Successfull");
		
		return array('status' => $response ? 'Success' : 'Error');
	}

	public function removeFavorite($username, $rid) {
		$uid = getUIDbyUsername($username, $this->mydb);
		if($uid == null || !is_int($rid)) {
			return array('status' => 'Error');
		}

		$query = "DELETE FROM bookmarks WHERE uid = ".$uid." AND rid = ".$rid.";";
		$response = handleQuery($query, $this->mydb, "Query Status: Remove Favorite Successfull");
		
		return array('status' => $response ? 'Success' : 'Error');
	}
	public function editRecipe($rid, $ingredients, $name, $username) {
		$esc_name = $this->mydb->real_escape_string($name);
		$recipe_array = getRecipeByRID($rid, $this->mydb);
		$response = '';
		if (!$recipe_array|| $recipe_array == NULL) {
			return array('status' => 'Error');
		}
		if (!$recipe_array['is_custom'] && $recipe_array['custom_author'] != $username) {
			echo "Creating New Custom Recipe".PHP_EOL;
			$formatted_labels =  implode(", ", getRecipeLabels($rid, $this->mydb));
			addRecipe($esc_name, $recipe_array['image'], $recipe_array['num_ingredients'], $ingredients, 
			$recipe_array['calories'], $recipe_array['servings'], $formatted_labels, $this->mydb, TRUE, $username);
			$status = $response ? 'Success' : 'Error';
			if ($status = 'Success') {
				$fetchQuery = "SELECT rid FROM recipes WHERE name = '".$esc_name."' AND custom_author = '".$username."' AND is_custom = 1";
				$fetchResponse = handleQuery($fetchQuery, $this->mydb, "Grab New RID Success");
				$newRID = $fetchResponse->fetch_assoc();

				print_r($newRID['rid']);
				print_r($this->addFavorite($username, $newRID['rid']));
			}

			return array('status' => $status);
		}
		else {
			echo "Updating Custom Recipe".PHP_EOL;
			$updateQuery = "UPDATE recipes SET name = '".$esc_name."', ingredients = '".$ingredients."', 
			custom_author = '".$username."' WHERE rid = ".$rid.";";
			$response = handleQuery($updateQuery, $this->mydb, "Query Status: Update Custom Recipe Successful");
			$status = $response ? 'Success' : 'Error';
			
			return array('status' => $status);
		}

	}

	//review functions
	public function addReview($username, $rid, $rate, $text) {
		$uid = getUIDbyUsername($username, $this->mydb);
		if($uid === null || !is_int($rid)) {
			return array('status' => 'Error');
		}
		if(isTwoDuplicatesFound($uid, $rid, 'uid', 'rid', 'reviews', $this->mydb)) {
			return array('status' => 'Error_Duplicate');
		}

		$query = "INSERT INTO reviews (uid, rid, rating, description) 
		VALUES (".$uid.", ".$rid.", ".$rate.", '".$text."');";
		$response = handleQuery($query, $this->mydb, "Query Status: Add Review Successfull");

		return array('status' => $response ? 'Success' : 'Error');
	}

	public function removeReview($rate_id) {
		if($rate_id == null || !is_int($rate_id)) {
			return array('status' => 'Error');
		}

		$query = "DELETE FROM reviews WHERE rate_id = ".$rate_id.";";
		$response = handleQuery($query, $this->mydb, "Query Status: Remove Favorite Successfull");
		
		return array('status' => $response ? 'Success' : 'Error');
	}

	//mealplan functions
	public function addMealPlan($array) { //Can only work with one exisitng mealplan
		$uid = getUIDbyUsername($array['username'], $this->mydb);
		$mp_array = $array;
		unset($mp_array['type'], $mp_array['username'], $mp_array['message']);
		
		$cid_query = "SELECT cid FROM mealplans WHERE uid = ".$uid.";";
		$mpResponse = handleQuery($cid_query, $this->mydb, 'Query Status: addMealPlan| fetch cid sucessful');
		$fetch_cid = $mpResponse->fetch_assoc();
		print_r($fetch_cid);
		if ($fetch_cid == NULL) {
			echo "NULLLL".PHP_EOL;
			$cid = null;
		} else {
		$cid = $fetch_cid['cid']; //grabs first row
		}
		if ($cid == null) { //create new mealplan if not exists
			$mp_name = "My Weekly Meal Plan";
			$esc_name = $this->mydb->real_escape_string($mp_name);
			//print_r("INSERT INTO mealplans (uid, mp_name) VALUES(".$uid.", '".$esc_name."');");
			$createMP_Response =  handleQuery("INSERT INTO mealplans (uid, mp_name) VALUES (".$uid.", '".$esc_name."');", $this->mydb, 'Query Status: addMealPlan| create MP successful');
			if (!$createMP_Response) {
				return array('status' => 'error');
			}
			$fetchResponse = handleQuery($cid_query, $this->mydb, "addMealPlan|createMP| fetch cid Successful");
			$newCID = $fetchResponse->fetch_assoc();
			$cid = $newCID['cid'];
		}
		else { //delete from exising mealplan
			handleQuery("DELETE FROM mealplan_entries WHERE cid = ".$cid."", $this->mydb, "Query Status: addMealPlan| delete assoc cid entires successful");
		}
		$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
		$meal_type = ['Breakfast', 'Lunch', 'Dinner'];
		$num = 0;
		foreach($mp_array as $key => $val) {
			$rid = "NULL";
			if($val != NULL) {
				$rid = $val;
			}
			addMealPlanEntry($cid, $val, $days[$num % count($days)], $meal_type[$num % count($meal_type)], $this->mydb);
			$num++;
		}
		return array('status' => 'success');
	}

	public function getRex($username) {
		$uid = getUIDbyUsername($username, $this->mydb);
		$labels = getUserPref($username, $this->mydb);

		$top_labels_query = "SELECT labels.label_name AS label, COUNT(*) AS score
		FROM bookmarks JOIN recipe_labels ON bookmarks.rid = recipe_labels.rid
		JOIN labels ON recipe_labels.label_id = labels.label_id WHERE bookmarks.uid = ".$uid."
		GROUP BY labels.label_name ORDER BY COUNT(*) DESC LIMIT 5";
		$topResponse = handleQuery($top_labels_query, $this->mydb, "Query Status: Top 5 Labels Successful");
		$top_five = $topResponse->fetch_all(MYSQLI_ASSOC);

		//print_r($top_five);
		foreach ($top_five as $row) {
			$label = $row['label'];
			//echo "old score: ".$row['score'].PHP_EOL;
			if (in_array($label, $labels)) {	
				$row['score'] *= 2;
				//echo "new score: ".$row['score'].PHP_EOL;
			}
		}
		usort($top_five, function ($a, $b) { return $b['score'] <=> $a['score']; });
		$rex_results = array();
		$rex1 = $this->checkRecipeByLabel($top_five[0]['label'], 15);
		$rex2 = $this->checkRecipeByLabel($top_five[1]['label'], 10);
		$rex3 = $this->checkRecipeByLabel($top_five[2]['label']);

		$rex_results[] = $rex1;
		$rex_results[] = $rex2;
		$rex_results[] = $rex3;

		return $rex_results;
	}

//Deployment Function

	public function recordIncomingBundle($name, $version, $machine, $bundle_status, $path, $cluster) { //return boolean

		$query = "INSERT INTO bundles VALUES ('".$name."', ".$version.", '".$bundle_status."', '".$machine."', '".$path."', 1, '".$cluster."');"; 
		//All new incoming bundles are automatically assigned as the new Current Versions
		$response = handleQuery($query, $this->mydb, "Query Status: Record Incoming Bundle Successful");

		return $response;
	}

	public function changeBundleStatus($name, $status) { //return boolean
		$query = "UPDATE bundles SET status = '".$status."' 
		WHERE name = '".$name."';";
		$response = handleQuery($query, $this->mydb, "Query Status: Change Bundle Status Successful");

		return $response;

	}

	public function changeCurrentVersion($name, $boolean) { //return boolean
		$c = $boolean ? 1 : 0;
		$query = "UPDATE bundles SET isCurrentVersion = ".$c."
		WHERE name = '".$name."';";
		$response = handleQuery($query, $this->mydb, "Query Status: Change Current Version Successful");

		return $response;
	}

	public function getBundleStatus($name) { //return imploded status string
		$query = "SELECT status FROM bundles WHERE name = '".$name."';";
		$response = handleQuery($query, $this->mydb, "Query Status: get Bundle Status Successful");

		return implode($response);

	}

	public function getCurrentVersion($machine, $cluster) { //return imploed name string
		$query = "SELECT name FROM bundles WHERE machine = '".$machine."' 
		AND cluster = '".$cluster."' AND isCurrentVersion = 1;";
		$response = handleQuery($query, $this->mydb, "Query Status: Count All Versions Successful");

		$response->fetch_array(MYSQLI_NUM);
		if ($response == null) {
			echo "No current version of ".$machine." in cluster ".$cluster." found".PHP_EOL;
			return null;
		}
		
		return implode($response);

	}

	public function getBundleList($machine, $cluster) { //return array of names
		$query = "SELECT name, status FROM bundles 
		WHERE machine = '".$machine."'
		AND cluster = '".$cluster."';";
		
		$response = handleQuery($query, $this->mydb, "Query Status: Get Bundle List Successful");
		if (!$response) {
			return array('status' => 'Error');
		}
		$response_arr = $response->fetch_all(MYSQLI_ASSOC);

		return $response_arr;
	}

	public function generateVersionNum($machine, $cluster) { //return int
		$query = "SELECT count(*) FROM bundles WHERE machine = '".$machine."' 
		AND cluster = '".$cluster."';";
		$response = handleQuery($query, $this->mydb, "Query Status: Count All Versions Successful");

		$num = implode($response->fetch_array(MYSQLI_NUM));
		return $num;

	}

	public function generateVersionNumAll($cluster) { //return int
		$machines = array("Database", "DMZ", "rabbitmq", "FrontEnd");
		$total_num = 0;
		foreach ($machines as $m) {
			$num = generateVersionNum($m, $cluster);
			if ($num > $total_num) {  //Replaces total_num with highest version num from all machines
				$total_num = $num;
			}
		}
		return $total_num;
	}
}


//For Testing  and debugging



/*$testObj = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

//Test Two Dim array recipes
$chickenRecipes = [
    [
        'name' => 'Lemon Herb Grilled Chicken',
        'image' => 'http://example.com/chicken1.jpg',
        'num_ingredients' => 7,
        'ingredients' => 'chicken breast, lemon juice, olive oil, garlic, rosemary, salt, pepper',
        'calories' => 250,
        'servings' => 4,
        'labels' => 'high-protein, gluten-free, dairy-free, low-carb'
    ],
    [
        'name' => 'Garlic Butter Chicken',
        'image' => 'http://example.com/chicken2.jpg',
        'num_ingredients' => 8,
        'ingredients' => 'chicken thighs, butter, garlic, paprika, thyme, salt, pepper, olive oil',
        'calories' => 320,
        'servings' => 4,
        'labels' => 'high-protein, keto-friendly, gluten-free'
    ],
    [
        'name' => 'Chicken Caesar Salad',
        'image' => 'http://example.com/chicken3.jpg',
        'num_ingredients' => 9,
        'ingredients' => 'grilled chicken, romaine lettuce, parmesan, croutons, olive oil, lemon juice, garlic, anchovy paste, pepper',
        'calories' => 380,
        'servings' => 2,
        'labels' => 'high-protein, gluten-free, dairy-free'
    ],
    [
        'name' => 'Honey Mustard Chicken',
        'image' => 'http://example.com/chicken4.jpg',
        'num_ingredients' => 7,
        'ingredients' => 'chicken breasts, dijon mustard, honey, garlic, olive oil, salt, pepper',
        'calories' => 290,
        'servings' => 4,
        'labels' => 'high-protein, gluten-free, dairy-free'
    ],
    [
        'name' => 'Chicken Stir-Fry',
        'image' => 'http://example.com/chicken5.jpg',
        'num_ingredients' => 11,
        'ingredients' => 'chicken breast, broccoli, bell peppers, soy sauce, ginger, garlic, sesame oil, rice vinegar, honey, red pepper flakes, sesame seeds',
        'calories' => 280,
        'servings' => 4,
        'labels' => 'high-protein, dairy-free, gluten-free'
    ],
    [
        'name' => 'Baked Parmesan Chicken',
        'image' => 'http://example.com/chicken6.jpg',
        'num_ingredients' => 6,
        'ingredients' => 'chicken breasts, parmesan cheese, breadcrumbs, garlic powder, olive oil, salt',
        'calories' => 310,
        'servings' => 4,
        'labels' => 'high-protein, gluten-free'
    ],
    [
        'name' => 'Chicken Tortilla Soup',
        'image' => 'http://example.com/chicken7.jpg',
        'num_ingredients' => 12,
        'ingredients' => 'chicken broth, shredded chicken, black beans, corn, diced tomatoes, onion, garlic, cumin, chili powder, lime juice, tortilla strips, avocado',
        'calories' => 220,
        'servings' => 6,
        'labels' => 'high-protein, high-fiber, dairy-free'
    ],
    [
        'name' => 'BBQ Chicken Skewers',
        'image' => 'http://example.com/chicken8.jpg',
        'num_ingredients' => 6,
        'ingredients' => 'chicken breast, BBQ sauce, bell peppers, red onion, olive oil, salt',
        'calories' => 270,
        'servings' => 4,
        'labels' => 'high-protein, dairy-free, gluten-free'
    ],
    [
        'name' => 'Chicken Alfredo',
        'image' => 'http://example.com/chicken9.jpg',
        'num_ingredients' => 8,
        'ingredients' => 'fettuccine, chicken breast, heavy cream, parmesan cheese, garlic, butter, salt, pepper',
        'calories' => 450,
        'servings' => 4,
        'labels' => 'high-protein'
    ],
    [
        'name' => 'Mediterranean Chicken',
        'image' => 'http://example.com/chicken10.jpg',
        'num_ingredients' => 9,
        'ingredients' => 'chicken thighs, olives, cherry tomatoes, feta cheese, oregano, lemon, garlic, olive oil, red onion',
        'calories' => 330,
        'servings' => 4,
        'labels' => 'high-protein, Mediterranean, gluten-free'
    ]
];

//$testObj->populateRecipe($chickenRecipes);

//$test_labels = ['high-protein'];
//print_r($testObj->checkRecipe('Chicken', $test_labels));
//print_r($testObj->checkRecipe('Caesar Salad'));

//$test_prefs = ['dairy-free', 'gluten-free', 'high-protein', 'Kosher'];
//$test_prefs = ['dairy-free', 'gluten-free', 'high-protein', 'Kosher'];
//print_r($testObj->changeUserPref('Bob', $test_prefs));
//print_r($testObj->addFavorite('Bob', 60));
//print_r($testObj->removeFavorite('Bob', 60));

//print_r($testObj->getUserDiet('Bob'));
//print_r($testObj->getUserFavorites('Bob'));

//print_r($testObj->getRex('Bob'));

//print_r($testObj->addReview('Bob', 56, 4, "Very Good!"));
//print_r($testObj->getUserReviews('Bob'));
//print_r($testObj->removeReview(2));

//print_r($testObj->changeUserPref('Bob', $test_labels));

/*showAr($testObj->registerAccount("Bob","bobby@gmail.com", "crabcake"));
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));
showAr($testObj->registerAccount("Larry2","Larry6@email.com", "snail"));

showAr($testObj->loginAccount("dummyuser", "dummypass"));    //TODO test validSession function
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));  */






?>
