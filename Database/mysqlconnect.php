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
			echo "failed to connect to database: ". $this->mydb->error . PHP_EOL;
			$dbConnectionStatus = false;
		}
		else {
			echo "successfully connected to database".PHP_EOL;
			$dbConnectionStatus = true;
		}
	}

	//Returns Bool
	public function getConnectionStatus () {
		return $this->dbConnectionStatus;
	}

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
}

	


//For Testing  and debugging

/*function showAr ($array) {
	foreach ($array as $key => $value) {
		echo "Key: $key; Value: $value\n";
	}
} 

$testObj = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

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

$test_labels = ['high-protein'];
//print_r($testObj->checkRecipe('Chicken', $test_labels));
print_r($testObj->checkRecipe('Caesar Salad'));

$test_prefs = ['dairy-free', 'gluten-free', 'high-protein', 'Kosher'];
//print_r($testObj->changeUserPref('Bob', $test_prefs));
print_r($testObj->addFavorite('Bob', 60));
print_r($testObj->removeFavorite('Bob', 60));

print_r($testObj->getUserDiet('Bob'));
print_r($testObj->getUserFavorites('Bob'));

print_r($testObj->addReview('Bob', 56, 4, "Very Good!"));
print_r($testObj->getUserReviews('Bob'));
//print_r($testObj->removeReview(2));

//print_r($testObj->changeUserPref('Bob', $test_labels));

/*showAr($testObj->registerAccount("Bob","bobby@gmail.com", "crabcake"));
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));
showAr($testObj->registerAccount("Larry2","Larry6@email.com", "snail"));

showAr($testObj->loginAccount("dummyuser", "dummypass"));    //TODO test validSession function
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));  */



?>
