<?php

	/***********************/
	/* connect to DataBase */
	/***********************/
	
	// datas to connect
	
	$host = 'mysql:host=localhost;dbname=ACJ;charset=utf8' ;
	$login = 'root' ;
	$mdp = '' ;
	
	// connection

	try
	{
		$bdd = new PDO($host,$login,$mdp);
	}
	catch(Exception $e)
	{
		die('Erreur : '.$e->getMessage());
	}
	
	if (isset($_POST['getNew'])) { // send the links which are less compared and with the nearest score (obliged to have an even number of items)
		$req = $bdd->query('SELECT link, score 
							FROM images 
							WHERE rounds=(SELECT MIN(rounds) 
							   			  FROM images) 
							ORDER BY score 
							LIMIT 0, 2') ;
		
		while($resp = $req->fetch()) {
			echo $resp['link'] . " " ;
		}
	}
?>