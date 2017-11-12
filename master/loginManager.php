<?php
	/**
		@file: loginManager
		@author Alexander Poganatz
		@author a_poganatz@outlook.com
		@version 0.1
		@brief Holds functions to be called when the client performs login operations
	*/

	session_start();

	require 'info.php';

	$notLoggedInJson = '{"IsLoggedIn": false}';
	$loggedInJson = '{"IsLoggedIn": true} ';

	/**
		@fn checkLogin
		@brief Checks if the user can log in and returns json to the client.
	*/
	function checkLogin()
	{
		if(!isset($_SESSION["username"]) || 
			!isset($_SESSION["password"]) )
		{
			echo $GLOBALS["notLoggedInJson"];
			return;
		}
		$conn = new mysqli($GLOBALS["servername"], $_SESSION["username"], $_SESSION["password"], 
		$GLOBALS["dbname"]);
		if($conn->connect_error)
		{
			echo $GLOBALS['notLoggedInJson'];
			return;
		}
		else
		{
			$conn->close();
			echo $GLOBALS['loggedInJson'];
		}
	}

	/**
		@fn login
		@brief Tries to login 
	*/
	function login($jsonString)
	{
		$data = json_decode($jsonString);
		$_SESSION['username'] = filter_var($data->Username, FILTER_SANITIZE_STRING);
		$_SESSION['password'] = filter_var($data->Password, FILTER_SANITIZE_STRING);

		checkLogin();
	}

	if($_SERVER['REQUEST_METHOD'] == "GET")
		checkLogin();
	else if($_SERVER['REQUEST_METHOD'] == "POST")
		login(stripslashes(file_get_contents("php://input")))

?>
