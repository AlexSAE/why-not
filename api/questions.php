<?php
// Example of MySQLi and Prepared Statements
// save this file as example.php
// access it with example.php?userid=13 for example

// Databse constants
define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "whynot");

function getQuestions($lat, $lon) {
	$questions = array();

	// New DB connection
	$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
	}

	// Prepared query with ? as placeholder for parameters
	$query = "SELECT question_id, question_text, question_votes FROM questions WHERE question_status = 1";

	// Get instance of statement
	$statement = $mysqli->stmt_init();

	// Prepare Query
	if ($statement->prepare($query)) {
	    // Execute the query
	    $statement->execute();

	    // Bind result variables
	    $statement->bind_result($question_id, $question_text, $question_votes);

	    // Fetch Value
	    while ($statement->fetch()) {
	    	$questions[] = array(
	    		'id'   => $question_id,
	    		'text' => $question_text,
	    		'votes' => $question_votes,
	    		'answers' => array()
	    	);
	    }

	    // Close statement
	    $statement->close();
	}

	// Close connection
	$mysqli->close();

	return $questions;
}

function getAnswers($question_id) {
	$answers = array();

	// New DB connection
	$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
	}
	 
	// Prepared query with ? as placeholder for parameters
	$query = "SELECT answer_id, answer_text FROM answers WHERE answer_question_id = ?";
	 
	// Get instance of statement
	$statement = $mysqli->stmt_init();
	 
	// Prepare Query
	if ($statement->prepare($query)) {
	 
	    // Bind Parameters - i for integer
	    $statement->bind_param('i', $question_id);
	 
	    // Execute the query
	    $statement->execute();
	 
	    // Bind result variables
	    $statement->bind_result($answer_id, $answer_text);
	 
	    // Fetch Value
	    while ($statement->fetch()) {
	        $answers[] = array(
	        	'id' => $answer_id,
	        	'text' => $answer_text
	        );
	    }
	 
	    // Close statement
	    $statement->close();
	}
	 
	// Close connection
	$mysqli->close();

	return $answers;
}

function addQuestion($user_id, $text, $lat, $lon) {
	// New DB connection
	$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
	}

	// Prepared query with ? as placeholder for parameters
	$query = "INSERT INTO `questions` (`question_id`, `question_user_id`, `question_text`, `question_votes`, `question_date`, `question_status`, `question_lon`, `question_lat`) VALUES (NULL, ?, ?, '0', CURRENT_TIMESTAMP, '1', ?, ?);";

	// Get instance of statement
	$statement = $mysqli->stmt_init();

	// Prepare Query
	if ($statement->prepare($query)) {

		$statement->bind_param('isii', $user_id, $text, $lon, $lat);

	    // Execute the query
	    $statement->execute();

	    // Close statement
	    $statement->close();
	}

	// Close connection
	$mysqli->close();
}


function addAnswer($question_id, $user_id, $text) {
	// New DB connection
	$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
	}

	// Prepared query with ? as placeholder for parameters
	$query = "INSERT INTO `answers` (`answer_id`, `answer_question_id`, `answer_user_id`, `answer_text`, `answer_date`) VALUES (NULL, ?, ?, ?, CURRENT_TIMESTAMP);";

	// Get instance of statement
	$statement = $mysqli->stmt_init();

	// Prepare Query
	if ($statement->prepare($query)) {

		$statement->bind_param('iis', $question_id, $user_id, $text);

	    // Execute the query
	    $statement->execute();

	    // Close statement
	    $statement->close();
	}

	// Close connection
	$mysqli->close();
}

$data = array();

$action = @$_GET['action'];
switch ($action) {
	case 'getQuestions':
		$questions = getQuestions(0,0);
		foreach ($questions as $q) {
			$q['answers'] = getAnswers($q['id']);
			$data[] = $q;
		}
		break;
	case 'addQuestion':
		$user_id = $_POST['user_id'];
		$text    = $_POST['text'];
		addQuestion($user_id, $text, 0, 0);
		$data = ['success'=> true];
		break;
	case 'addAnswer':
		$question_id = $_POST['question_id'];
		$user_id     = $_POST['user_id'];
		$text        = $_POST['text'];
		addAnswer($question_id, $user_id, $text);
		$data = ['success'=> true];
		break;
}

echo json_encode($data);

//addQuestion(13, 'test iz fajla', 0, 0);

/*
$questions = getQuestions(0,0);
$data = array();

foreach ($questions as $q) {
	$q['answers'] = getAnswers($q['id']);
	$data[] = $q;
}

echo json_encode($data);
*/