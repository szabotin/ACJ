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

function incrementLAndDisplay(linkLeft, linkRight) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			incrementRoundAndDisplay(linkLeft, linkRight) ;
		}
	} ;
	
	xhr.open("POST", "handlingDataText.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("incrementS=" + linkLeft);
}

function incrementRAndDisplay(linkLeft, linkRight) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			incrementRoundAndDisplay(linkLeft, linkRight) ;
		}
	} ;
	
	xhr.open("POST", "handlingDataText.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("incrementS=" + linkRight);
}

// increment the round of the 2 items

function incrementRoundAndDisplay(linkLeft, linkRight) {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() { 
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) { // once the request is finished
			displayNewItems() ; // we have to display the new items
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
	var xhr = getXMLHttpRequest();
	
	xhr.open("POST", "handlingDataText.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("l1=" + linkLeft + "&" + "l2=" + linkRight + "&" + "win=" + winner);
}

/***********************************/
/*  deal with results_matrix table */
/***********************************/

function initResultsMatrix() {
	var xhr = getXMLHttpRequest();
	
	xhr.open("POST", "handlingDataText.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("initRM=true") ;
}

/*******************************************/
/*  functions to display an item on screen */
/*******************************************/

// get the link of the two new images and display it on the screen

function displayNewItems() {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			newLinks = xhr.responseText.split(" ") ; // from "link1 link2" gets newLinks = ["link1", "link2", ""]
			modifyLinks(newLinks) ; 
			dispLinkTest(newLinks) ; // comment if you don't want to display the links above the items
		}
	} ;
	
	xhr.open("POST", "handlingItems.php", true) ;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded") ;
	xhr.send("getNew=true") ;
	/*var form = new FormData();
	form.append('champ1', 'valeur1');
	form.append('champ2', 'valeur2');*/
}

function modifyLinks(links) {
	// modify the source of items
	iteml = document.getElementById("itemLeft") ;
	itemr = document.getElementById("itemRight") ;
	
	iteml.src = links[0] ;
	itemr.src = links[1] ;
}

function dispLinkTest(links) { // display the links for tests
	displl = document.getElementById("dispLinkL") ;
	displr = document.getElementById("dispLinkR") ;
	
	displl.innerHTML = links[0] ;
	displr.innerHTML = links[1] ;
}

/***************/
/* compute ACJ */
/***************/

function computeACJ() {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			console.log(xhr.responseText) ;
		}
	} ;
	
	xhr.open("POST", "computeACJ.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("compute=true") ;
}

/**********************/
/*  function readData */
/**********************/

function readData(sData) {
	console.log(sData);
}