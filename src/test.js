var it_disp = 0 ; // global variable to know if the items are displayed. We have to do nothing when if we click on a button while items are not displayed.  -> must be improve. Bad code

var nbRandRounds = 4 ; // Number of rounds we want to select items randomly
var nbScoreRounds = 10 ; // Number of rounds we want to select items by score
var thresholdPercentage = 0.75 ; // Threshold to select the match

// display Items first

selectAndDisplayNewItems('random') ;

// button declarations

var butL = document.getElementById("bL") ; // left button
var butR = document.getElementById("bR") ; // right button
var newACJRound = document.getElementById("compute_ACJ") ; // button to compute a new Round of ACJ

/****************/
/* click events */
/****************/

butL.addEventListener('click', function() { // when you click on the left button
	
	if (it_disp) { // if the items are displayed, we can click on the button, else, we must do nothing
		
		var linkLeft = document.getElementById("itemLeft").src ;
		var linkRight = document.getElementById("itemRight").src ;
		
		it_disp = 0 ;
		
		// add the comparison in dataBase, between link left, link right and the winner is link right
		addMatch(linkLeft, linkRight, linkLeft) ;
		
		// this function will increment the score and after the rounds of the items
		// and display after doing that, because the picture chosen have to wait the
		// incrementation of the score in the database
		
		incrementLSelectAndDisplay(linkLeft, linkRight) ; // the 'it_disp' variable is set as 1 in the displayNewItems() function, after the AJAX request is done
	}
	
	else {
		alert ("Wait !!! ") ;
	}
})

butR.addEventListener('click', function() { // when you click on the right button

	if (it_disp) { // if the items are displayed, we can click on the button
		
		var linkLeft = document.getElementById("itemLeft").src ;
		var linkRight = document.getElementById("itemRight").src ;
		
		it_disp = 0 ; 
		
		// add the comparison in dataBase, between link left, link right and the winner is link right
		addMatch(linkLeft, linkRight, linkRight) ;
		
		// this function will increment the score and after the rounds of the items
		// and display after doing that, because the picture chosen have to wait the
		// incrementation of the score in the database
		
		incrementRSelectAndDisplay(linkLeft, linkRight) ; // the 'it_disp' variable is set as 1 in the displayNewItems() function, after the AJAX request is done
	}
	
	else {
		alert ("Wait !!! ") ;
	}
	
})


newACJRound.addEventListener('click', function() {
	computeACJ() ;
})


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
			giveInformationBeforeSelect() ;
		}
	} ;
	
	xhr.open("POST", "reset.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("reset=true") ;
}

function resetDisplay() {
	var box = document.querySelectorAll(".compareBox") ;
	box[0].style.display = "flex" ;
	var finish = document.getElementById("finish") ;
	finish.style.display = "none" ;
}

reset.addEventListener('click', function() {
	// have to display the new items after reset the datas
	initResultsMatrix() ;
	resetAndDisplay() ;
	resetDisplay() ;
})

// TODO if have to add item
//add item & reinit matrix 
//add item only 
// launch in the same time increment score and increment round, but launch displayNewId after the finishing of the 2 functions
//not be able to do something if item is not displayed