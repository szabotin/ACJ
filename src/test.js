// display Items first

displayNewItems() ;

// button declarations

var butL = document.getElementById("bL") ;
var butR = document.getElementById("bR") ;
var newRound = document.getElementById("compute_ACJ") ;

/****************/
/* click events */
/****************/

butL.addEventListener('click', function() {

	var linkLeft = document.getElementById("itemLeft").src ;
	var linkRight = document.getElementById("itemRight").src ;
	
	// add the comparison in dataBase, between link left, link right and the winner is link right
	addMatch(linkLeft, linkRight, linkLeft) ;
	
	// this function will increment the score and after the rounds of the items
	// and display after doing that, because the picture chosen have to wait the
	// incrementation of the score in the database
	
	incrementLAndDisplay(linkLeft, linkRight) ;
	
	}
)

butR.addEventListener('click', function() {
	
	var linkLeft = document.getElementById("itemLeft").src ;
	var linkRight = document.getElementById("itemRight").src ;
	
	// add the comparison in dataBase, between link left, link right and the winner is link right
	addMatch(linkLeft, linkRight, linkRight) ;
	
	// this function will increment the score and after the rounds of the items
	// and display after doing that, because the picture chosen have to wait the
	// incrementation of the score in the database
	
	incrementRAndDisplay(linkLeft, linkRight) ;
	
	}
)


newRound.addEventListener('click', function() {
		computeACJ() ;
	}
)


/*******************************************/
/*       functions to reset database,      */ 
/*  if you don't want to use, just comment */
/*        until the end of the file        */
/*******************************************/

var reset = document.getElementById("resetButton") ;

function resetAndDisplay() {
	var xhr = getXMLHttpRequest();
	
	// we have to display the new items when the dataBase would be reset 
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			displayNewItems() ; 
		}
	} ;
	
	xhr.open("POST", "reset.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("reset=true") ;
}

reset.addEventListener('click', function() {
	// have to display the new items after reset the datas
	resetAndDisplay() ;
	}
)

// TODO if have to add item
//add item & reinit matrix 
//add item only 
// launch in the same time increment score and increment round, but launch displayNewId after the finishing of the 2 functions
//not be able to do something if item is not displayed