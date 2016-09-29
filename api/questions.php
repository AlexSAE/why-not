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

$questions = getQuestions(0,0);
$data = array();

foreach ($questions as $q) {
	$q['answers'] = getAnswers($q['id']);
	$data[] = $q;
}

echo json_encode($data);
