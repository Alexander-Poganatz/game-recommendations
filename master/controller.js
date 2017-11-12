/**
	@file: controller.js
	@author Alexander Poganatz
	@version 0.1
	@brief Manages displaying data and CRUD operations between client and server
*/

var app = new Vue({
	el: "#app",
	data: {
		games: [],
		statusText: "",
		selectedGame: {},
		modalDisplay: 'none',
		loggedIn: false,
		loginInfo : { Username: "", Password: ""}
	},
	mounted: function()
	{
		this.checkLogin();
	},
	methods: {
		/**
			@fn fetchData
			@brief fetches all games from the server
		*/
		fetchData : function() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function()
			{
				if(this.readyState == 4 && this.status == 200) 
				{
					var data = JSON.parse(xhttp.responseText);
					if(data.error != undefined)
						app.statusText = data.error;
					else 
					{
						data.sort( (a, b) => {return a.Title.localeCompare(b.Title);});
						app.games = data;
					}
				}
			}
			xhttp.open("GET", "game_manager.php", true);
			xhttp.onerror = this.setAjaxError;
			xhttp.send();
		},
		/**
			@fn setAjaxError
			@brief Sets the status text that there was a communication error
		*/
		setAjaxError : function ()
		{
			statusText = "Error communicating with the server";
		},
		/**
			@fn editGame
			@brief Finds the selected game from the data and displays it for editing
		*/
		editGame : function(id)
		{
			this.selectedGame = this.games.find(x => x.ID == id);
			app.modalDisplay = 'block';
		},
		/**
			@fn putOrPost
			@brief Adds or modifies a game to the server
		*/
		putOrPost: function()
		{
			let pOrp = this.selectedGame.ID == 0 ? "PUT" : "POST";
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() 
			{
				if(this.readyState == 4 && this.status == 200)
				{
					app.statusText = this.responseText;
					app.fetchData();
					app.modalDisplay = 'none';
				}
			}
			xhttp.open(pOrp, "game_manager.php", true);
			xhttp.setRequestHeader("Content-Type", " application/json");
			xhttp.onerror = this.setAjaxError;
			xhttp.send(JSON.stringify(app.selectedGame));
		},
		/**
			@fn deleteGame
			@brief Remove the selected game from the server.
		*/
		deleteGame: function()
		{
			var  id = app.selectedGame.ID;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function()
			{
				if(this.readyState == 4 && this.status == 200)
				{
					app.statusText = this.responseText;
					app.fetchData();
					app.modalDisplay = 'none';
				}
			}
			xhttp.open("DELETE", "game_manager.php", true);
			xhttp.setRequestHeader("Content-Type", " application/json");
			xhttp.onerror = this.setAjaxError;
			xhttp.send('{"ID": "' + id + '"}');
		},
		/**
			@fn newGame
			@brief Sets the selectedGame to a empty Game object and displays it for editing
		*/
		newGame: function()
		{
			app.selectedGame = {ID: 0, Title: "", Publisher: "", Developer: "", Score: "", Comment: ""};
			app.modalVisible = !app.modalVisible;
			app.modalDisplay = 'block';
		},
		/**
			@fn cancel
			@brief Removes the modal from being displayed
		*/
		cancel: function()
		{
			app.modalDisplay = 'none';
		},
		/**
			@fn updateClient
			@brief tells the server to generate a new client json file
		*/
		updateClient: function()
		{
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function()
			{
				if(this.readyState == 4 && this.status == 200)
				{
					app.statusText = this.responseText;
				}
			}
			xhttp.open("GET", "createJSON.php", true);
			xhttp.onerror = this.setAjaxError;
			xhttp.send();
		},
		/**
			Tries to log in to the server
		*/
		logIn: function()
		{
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function()
			{
				if(this.readyState == 4 && this.status == 200)
				{
					var data = JSON.parse(this.responseText);
					app.loggedIn = data.IsLoggedIn;
					if(data.IsLoggedIn)
						app.fetchData();
				}
			}
			xhttp.open("POST", "loginManager.php", true);
			xhttp.onerror = this.setAjaxError;
			xhttp.send(JSON.stringify(this.loginInfo));
		},
		/**
			@fn checkLogin
			@brief asks the server if we are logged in. If true, fetch the games.
		*/
		checkLogin: function()
		{
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function()
			{
				if(this.readyState == 4 && this.status == 200)
				{
					var data = JSON.parse(this.responseText);
					app.loggedIn = data.IsLoggedIn;
					if(data.IsLoggedIn)
						app.fetchData();
				}
			}
			xhttp.open("GET", "loginManager.php", true);
			xhttp.onerror = this.setAjaxError;
			xhttp.send();
		}
	}
})
