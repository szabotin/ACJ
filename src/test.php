<!DOCTYPE html>

<html lang = "fr">
	<head>
		<title> Preferences </title>
		
		<meta charset = "utf-8" />
		<meta name="auteur" content = "SZABO Clement" />
		
		<link rel = "stylesheet" type = "text/css" media = "all" href = "../styles/Reset.css"/>
		<link rel = "stylesheet" type = "text/css" media = "screen" href = "../styles/GeneralStyle.css"/>
	
	</head>
	
	<body>
	
		<header>
			<h1> Click on what you prefer </h1>
		</header>
		
		<main>
			<div class = "compareBox"> 
				<div class = "button" id = "boxLeft">
					<p id="dispLinkL"> </p>
					<img id="itemLeft">
					<form method="POST" action="index.php" id="buttonLeft">
						<input type="button" value="I prefer this one !" id = "bL"/>																		
					</form>
				</div>
				
				<div id = "boxRight">
					<p id="dispLinkR"> </p>
					<img id="itemRight">
					<form method="POST" action="index.php" id="buttonRight">
						<input type="button" value="I prefer this one !" id = "bR">
					</form>
				</div>			
			</div>
			
			<p id = "finish"> Merci pour votre partcipation ! </p>
			
			<input type="button" value="New ACJ Round" id = "compute_ACJ"/>
			
			<input type="button" value="Reset Data Base" id = "resetButton"/>
			
		</main>
		
		<script src="ACJ.js"></script>
		<script src="test.js"></script>
		
	</body>
</html>