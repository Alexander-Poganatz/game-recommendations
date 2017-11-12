/**
	@file app.js
	@author Alexander Poganatz
	@version 0.1
	@brief Holds the controller for user interaction
*/

var app = new Vue({
	el: '#app',
	data:
	{
		games: [],
		selectedGame: {},
		modalDisplayMode: 'none'
	},
	mounted: function() 
	{
		this.games = data.slice(0);
		this.games.sort( function(x, y) { return x.Title.localeCompare(y.Title);});
	},
	methods: {
		/**
			@fn toggleModal
			@brief Toggles the html modal
		*/
		toggleModal : function()
		{
			if(this.modalDisplayMode == 'none')
				this.modalDisplayMode = 'block';
			else
				this.modalDisplayMode = 'none';
		},
		/**
			@fn displayModal
			@brief finds the selected game based on ID and displays the modal.
		*/
		displayModal : function(id)
		{
			this.selectedGame = data.find(x => x.ID == id);
			this.toggleModal(); 
		}
	}
})
