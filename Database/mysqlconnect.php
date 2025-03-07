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
	
	//recipes
	public function checkRecipe($keywords, $labels = '') {
		
		$formatted_labels = '';
		$formatted_search = '';

		$serch_arr = array_filter(array_map('trim', explode(' ', $keywords)));
		$formatted_search = array_map(function($keyword) {return '+' . $keyword . '*'; }, $serch_arr);

		if ($labels !== '') {
			$label_arr = array_map('trim', explode(',', $labels));
			$formatted_labels = array_map(function($label) { return "'" . $label . "'";}, $label_arr);
		}

		$label_query = ($labels === '') ? '' : "INNER JOIN (
            SELECT recipe_labels.rid 
            FROM recipe_labels 
            INNER JOIN labels ON recipe_labels.label_id = labels.label_id
            WHERE labels.label_name IN (" . implode(',', $formatted_labels) . ")
            GROUP BY recipe_labels.rid
          ) AS filtered_rids ON recipes.rid = filtered_rids.rid";

		$query = "SELECT recipes.rid, recipes.name, recipes.image, recipes.num_ingredients, recipes.ingredients, recipes.calories, recipes.servings, GROUP_CONCAT(DISTINCT labels.label_name SEPARATOR ', ') AS labels_str
		FROM recipes INNER JOIN recipe_labels  ON recipes.rid = recipe_labels.rid INNER JOIN labels ON recipe_labels.label_id = labels.label_id
		".$label_query." WHERE MATCH(recipes.name) AGAINST ('+".$keywords."*' IN BOOLEAN MODE) 
		GROUP BY recipes.rid;";

		$response = handleQuery($query, $this->mydb, "Query Status: Check Recipe Successfull");
		if ($response === false) {
			echo "ERROR";
			return $response;
		}
		$response_arr = $response->fetch_all();

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

	public function addFavorite($uid, $rid) {
		
	}
}

	


//For Testing  and debugging
/*
function showAr ($array) {
	foreach ($array as $key => $value) {
		echo "Key: $key; Value: $value\n";
	}
} 

function showTwoAR ($array) {
	foreach($array as $row) {
		showAr($row);
		echo "\n";
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

showTwoAr($testObj->checkRecipe('Chicken', 'keto-friendly'));
showTwoAr($testObj->checkRecipe('Caesar Salad'));

/*showAr($testObj->registerAccount("Bob","bobby@gmail.com", "crabcake"));
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));
showAr($testObj->registerAccount("Larry2","Larry6@email.com", "snail"));

showAr($testObj->loginAccount("dummyuser", "dummypass"));    //TODO test validSession function
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));  */



?>
