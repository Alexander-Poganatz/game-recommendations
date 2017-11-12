<?php
	/**
		@file: game_manager_functions.php
		@author Alexander Poganatz
		@author a_poganatz@outlook.com
		@version 0.1
		@brief Holds the CRUD operations between database and client
	*/

	require 'game.php';
	require 'info.php';

	/**
		@fn validateGame 
		@brief Sanitizes the json data from the client and stores it in the Game object
		@param $game [out] The Game object to store the sanitized information
		@param $data [in] the data from the client converted with json_decode
		@return FALSE if there is an error and TRUE on success
	*/
	function validateGame($game, $data)
	{
		$game->ID = filter_var($data->ID, FILTER_VALIDATE_INT);
		if($game->ID === FALSE)
			return FALSE;
		
		$game->Title = filter_var($data->Title, FILTER_SANITIZE_STRING);
		if($game->Title === FALSE)
			return FALSE;
				
		$game->Developer = filter_var($data->Developer, FILTER_SANITIZE_STRING);
		if($game->Developer === FALSE)
			return FALSE;

		$game->Publisher = filter_var($data->Publisher, FILTER_SANITIZE_STRING);
		if($game->Publisher === FALSE)
			return FALSE;

		$game->Comment = filter_var($data->Comment, FILTER_SANITIZE_STRING);
		if($game->Comment === FALSE && strlen($game->Comment) !== 0)
			return FALSE;
		
		$game->Score = filter_var($data->Score, FILTER_VALIDATE_INT);
		if($game->Score === FALSE && $game->Score !== 0)
			return FALSE;
		
		return TRUE;
	}

	/**
		@fn getGames
		@brief Gets the games from the server
		@return A string either contained a JSON error object or an array of Game objects converted to a string
	*/
	function getGames()
	{
		$conn = new mysqli($GLOBALS["servername"], $_SESSION["username"], $_SESSION["password"], $GLOBALS["dbname"]);
		if($conn->connect_error)
		{
			return "{ 'error': 'Error connecting to the db'}";
		}
		$sql = "SELECT * FROM games";
		$result = $conn->query($sql);
		if($result->num_rows > 0)
		{
			$dataArray = array();
			while($row = $result->fetch_assoc())
			{
				$game = new Game;
				$game->ID = $row["ID"];
				$game->Title = htmlspecialchars_decode($row["Title"], ENT_QUOTES);
				$game->Publisher = htmlspecialchars_decode($row["Publisher"], ENT_QUOTES);
				$game->Developer = htmlspecialchars_decode($row["Developer"], ENT_QUOTES);
				$game->Comment = htmlspecialchars_decode($row["Comment"], ENT_QUOTES);
				$game->Score = $row["Score"];
				$dataArray[] = $game;
			}
			return json_encode($dataArray);
		}
		else
		{
			return "{'error':'No data'}";
		}
 		$conn->close();
	}

	/**
		@fn postGame
		@brief Updates a game
	*/
	function postGame()
	{
		$postData = json_decode(file_get_contents("php://input"));
		if($postData == NULL)
		{
			echo "Unable to get data";
			return;
		}
		$game = new Game;

		if(validateGame($game, $postData) === FALSE)
		{
			echo "Invalid data";
			return;
		}

		$conn = new mysqli($GLOBALS["servername"], $_SESSION["username"], $_SESSION["password"], $GLOBALS["dbname"]);
		if($conn->connect_error)
		{
			echo "Error connecting to the db";
			return;
		}

		$preparedStatement = $conn->prepare("UPDATE games SET Title = ?, Publisher = ?, Developer = ?, 
			Comment = ?, Score = ? WHERE ID = ?" );

		$preparedStatement->bind_param("ssssii", $game->Title, $game->Publisher, $game->Developer,
			$game->Comment, $game->Score, $game->ID);

		if(!$preparedStatement->execute())
			echo "Failed to update data for $game->Title";
		else
			echo "Updated $game->Title";
		$conn->close();
	}

	/**
		@fn insertGame
		@brief adds a game to the data base
	*/
	function insertGame()
	{
		$postData = json_decode(file_get_contents("php://input"));
		$game = new Game;

		if(validateGame($game, $postData) === FALSE)
		{
			echo "Invalid data";
			return;
		}

		$conn = new mysqli($GLOBALS["servername"], $_SESSION["username"], $_SESSION["password"], $GLOBALS["dbname"]);
		if($conn->connect_error)
		{
			echo "Error connecting to the db";
			return;
		}

		$preparedStatement = $conn->prepare("INSERT INTO games( Title,Publisher, Developer , 
			Comment, Score) VALUES(?,?,?,?,? )");

		$preparedStatement->bind_param("ssssi", $game->Title, $game->Publisher, $game->Developer,
			$game->Comment, $game->Score);

		if(!$preparedStatement->execute())
			echo "Failed to insert data for $game->Title";
		else
			echo "Created $game->Title";
		$conn->close();
	}

	/**
		@fn deleteGame
		@brief removes a game from the server.
	*/
	function deleteGame()
	{
		$postData = json_decode(stripslashes(file_get_contents("php://input")));

		$id = filter_var($postData->ID, FILTER_VALIDATE_INT);

		if($id === FALSE)
		{
			echo "Invalid ID";
			return;
		}

		$conn = new mysqli($GLOBALS["servername"], $_SESSION["username"], $_SESSION["password"], $GLOBALS["dbname"]);
		if($conn->connect_error)
		{
			echo "Error connecting to the db";
			return;
		}

		$preparedStatement = $conn->prepare("DELETE FROM games WHERE ID = ? ");

		$preparedStatement->bind_param("i", $id);

		if(!$preparedStatement->execute())
			echo "Failed to delete the game";
		else
			echo "Deleted the game";
		$conn->close();
	}
?>
