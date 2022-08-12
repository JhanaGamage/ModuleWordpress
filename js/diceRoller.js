(function() {
	var dice = {
		roll: function (sides, rolls) {  
			console.log(sides, rolls)
			var throws = 0;        
			for (let index = 0; index < rolls; index++) {
				let roll = Math.floor(Math.random() * sides) + 1;
				console.log(roll)
				throws += roll; 
			}
			return throws;
		}
	}
	
	function printNumber(number) {
		console.log(number);
	
		var placeholder = document.getElementById('placeholder');
		placeholder.innerHTML = number;
	}
	
	var button = document.getElementById('roll-button');
	var rollings = document.getElementById('rollings');
	console.log('hello');
	
	button.onclick = function() {
		var diceSides = document.getElementById("dice-select").value;
		var rolls = document.getElementById("roll-step").value;
		var result = dice.roll(diceSides, rolls);
		console.log(result);
		printNumber(result);

		var uid = '<?php echo get_current_user_id(); ?>';

	    
		fetch("wp-content/plugins/diceRolling/add.php", {

        	method: "POST",
        	headers: {
        	  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        	},
        	body: `dice=${diceSides}&rolls=${rolls}&uid=${uid}`

      	})
      	.then((response) => response.text())
      	.then((res) => (document.getElementById("placeholder").innerHTML = res));

	};	
})();
