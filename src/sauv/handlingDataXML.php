<?php 
	header("Content-Type: text/plain");
	
	// datas to connect to phpMyAdmin
	
	$host = 'mysql:host=localhost;dbname=ACJ;charset=utf8' ;
	$login = 'root' ;
	$mdp = '' ;
	
	// connect to database
	// echo( "<link id = \"linkLeft\" href=\"" . $_POST['b1'] . "\"/>") ;
	
	try
	{
		$bdd = new PDO($host,$login,$mdp);
	}
	catch(Exception $e)
	{
			die('Erreur : '.$e->getMessage());
	}
	
	/*echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	echo "<root>" ;*/
	if (isset($_POST['send'])) { // send the links which are less compared and with the nearest score (obliged to have an even number of items)
		$req = $bdd->query('SELECT link, score 
							FROM images 
							WHERE rounds=(SELECT MIN(rounds) 
							   			  FROM images) 
							ORDER BY score 
							LIMIT 0, 2') ;
		$i = 1 ;
		
		while($resp = $req->fetch()) {
			/*echo "<link".$i.">" ;     //" value = \"" . $resp['link'] . "\"/>" ;
			echo $resp['link'] ;
			echo "</link>" ;
			$i = $i + 1 ;*/
			echo $resp['link'] . " " ;
		}
	}
	//echo "</root>" ;
?>