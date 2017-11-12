<?php
	/**
		@file: game_manager.php
		@author Alexander Poganatz
		@author a_poganatz@outlook.com
		@version 0.1
		@brief calls the approipate function depending on the request type
	*/
	session_start();
	require 'game_manager_functions.php';

	if($_SERVER['REQUEST_METHOD'] === "GET")
		echo getGames();
	if($_SERVER['REQUEST_METHOD'] === "POST")
		postGame();
	if($_SERVER['REQUEST_METHOD'] === "PUT")
		insertGame();
	if($_SERVER['REQUEST_METHOD'] === 'DELETE')
		deleteGame();
?>
