// open XMLHttpRequest correctly depending on the browser

function getXMLHttpRequest() {
	var xhr = null;
	
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} 
	
	else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	
	return xhr;
}


/*********************************************/
/*  functions which makes link with database */
/*********************************************/


/***************************/
/*  deal with images table */
/***************************/

// increment the score of the left item

function incrementLSelectAndDisplay(linkLeft, linkRight) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			incrementRoundSelectAndDisplay(linkLeft, linkRight) ;
		}
	} 
	
	xhr.open("POST", "handlingDataText.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("incrementS=" + linkLeft);
}

function incrementRSelectAndDisplay(linkLeft, linkRight) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			incrementRoundSelectAndDisplay(linkLeft, linkRight) ;
		}
	} ;
	
	xhr.open("POST", "handlingDataText.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("incrementS=" + linkRight);
}

// increment the round of the 2 items

function incrementRoundSelectAndDisplay(linkLeft, linkRight) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() { 
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) { // after the request is finished
			selectAndDisplayNewItems() ;
		}
	} ;
	
	xhr.open("POST", "handlingDataText.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("l1=" + linkLeft + "&" + "l2=" + linkRight);
}

/****************************/
/*  deal with results table */
/****************************/

// add a new line for each comparaison

function addMatch(linkLeft, linkRight, winner) {
	var xhr = getXMLHttpRequest() ;
	
	xhr.open("POST", "handlingDataText.php", true) ;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded") ;
	xhr.send("l1=" + linkLeft + "&" + "l2=" + linkRight + "&" + "win=" + winner) ;
}

/***********************************/
/*  deal with results_matrix table */
/***********************************/

function initResultsMatrix() {
	var xhr = getXMLHttpRequest() ;
	
	xhr.open("POST", "handlingDataText.php", true) ;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded") ;
	xhr.send("initRM=true") ;
}

/*****************************************************************/
/*  functions to select the new items and display them on screen */
/*****************************************************************/

// get the link of the two new images and display it on the screen

function selectAndDisplayNewItems() {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			console.log(xhr.responseText) ;
			it_selected = xhr.responseText.split(" ") ; // from "link1 link2" gets it_selected = ["link1", "link2", ""]
			modifyLinks(it_selected) ; 
			dispLinkTest(it_selected) ; // comment if you don't want to display the links above the items
			//setTimeout(function(){it_disp = 1 ;}, 1000) ; // the time of the choice must be over than 5 seconds
			// displayFinishMessage() ; To display if we have finished the ACJ
			it_disp = 1 ;
		}
	} ;
	
	xhr.open("POST", "selectItems.php", true) ;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded") ;
	xhr.send("PM=true") ;
	
	/*var form = new FormData();
	form.append('champ1', 'valeur1');
	form.append('champ2', 'valeur2');*/
}

// to modify the link of the pictures which will be displayed

function modifyLinks(links) {
	// modify the source of items
	iteml = document.getElementById("itemLeft") ;
	itemr = document.getElementById("itemRight") ;
	
	iteml.src = links[0] ;
	itemr.src = links[1] ;
}

// display the items depends on the links

function dispLinkTest(links) { // display the links for tests
	displl = document.getElementById("dispLinkL") ;
	displr = document.getElementById("dispLinkR") ;
	
	displl.innerHTML = links[0] ;
	displr.innerHTML = links[1] ;
}

/************************************/
/* functions related to compute ACJ */
/************************************/

function computeACJ() {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			console.log(xhr.responseText) ;
			giveInformationBeforeSelect() ; // we have to have compute the ACJ before compute the information
		}
	} ;
	
	xhr.open("POST", "computeACJ.php", true) ;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded") ;
	xhr.send("compute=true") ;
}

/*****************************************************************************************/
/* functions related to the progressive method, method which is used to select the items */
/******************************************************************************************/

// the information will be useful for the progressive method

function giveInformationBeforeSelect() {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			console.log(xhr.responseText) ;
			selectAndDisplayNewItems() ;
		}
	} ;
	

	
	xhr.open("POST", "computeACJ.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("info=true") ;
}

/****************************/
/* when the ACJ is finished */
/****************************/

function displayFinishMessage() {
	var box = document.querySelectorAll(".compareBox") ;
	box[0].style.display = "none" ;
	var finish = document.getElementById("finish") ;
	finish.style.display = "block" ;
}