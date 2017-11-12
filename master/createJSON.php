<?php
	/**
		@file: createJSON.php
		@author Alexander Poganatz
		@author a_poganatz@outlook.com
		@version 0.1
		@brief Gets the games from the data base and put it in a json file for the clients.
	*/
	session_start();
	require 'game_manager_functions.php';

	#test connection
	$conn = new mysqli($GLOBALS["servername"], $_SESSION["username"], $_SESSION["password"], $GLOBALS["dbname"]);
	if($conn->connect_error)
		echo "Unable to connect to the database";
	else
	{
		$conn->close();
		$myfile = fopen("../data.js", "w") or die("Unable to open file");
		fwrite($myfile, "var data = " . getGames() . ";");
		fclose($myfile);
		echo "Wrote data to the client file";
	}
?>
