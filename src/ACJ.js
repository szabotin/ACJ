/*var butL = document.getElementById("buttonLeft") ;
	
butL.addEventListener('click', function() {
	alert('clic gauche') ;
	}
)

var butR = document.getElementById("buttonRight") ;
	
butR.addEventListener('click', function() {
	alert('clic droit') ;
	}
)*/
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
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	
	return xhr;
}

var butL = document.getElementById("bL") ;
var butR = document.getElementById("bR") ;

butL.addEventListener('click', function() {
	var xhr = getXMLHttpRequest() ;
	
	alert('clic gauche') ;
	xhr.open("POST", "handlingData.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("b1=true");
	}
)

butR.addEventListener('click', function() {
	var xhr = getXMLHttpRequest() ;
	
	alert('clic droit') ;

	xhr.open("POST", "handlingData.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("b2=true");
}
)



