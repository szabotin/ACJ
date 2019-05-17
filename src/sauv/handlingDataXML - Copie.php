<?php 
	header("Content-Type: text/xml");
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
	
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"";
	echo "<root>" ;
	
	isset($_POST['send']) {
		
		// get the number of picture
		$qnbpic = $bdd->query('SELECT COUNT(*) AS nbp FROM images') ; 
		$nbpic = $qnbpic->fetch()['nbp'] ;
	
		// get the minimum of rounds
		$response = $bdd->query('SELECT MIN(rounds) AS mini FROM images') ;
		$mini = $response->fetch()['mini'] ; 

		// we have to have the number of images which are less compared
		$req = $bdd->prepare('SELECT COUNT(*) AS nbmin FROM images WHERE rounds=?') ;
		$req->execute(array($mini)) ;
		$nb = $req->fetch()['nbmin'] ;
		if($nb == 1) { // we have to compare with one picture which has one more round 
								
		}
		else { // compare images with the same score
			// have the links which have the minimum of rounds
			$req = $bdd->prepare('SELECT link, score FROM images WHERE rounds=? ORDER BY score LIMIT 0, 2') ;
			$req->execute(array($mini)) ;
									
			$i = 1 ;

			while($resp = $req->fetch()) {
				if ($i == 1)
					$linkLeft = $resp['link'] ;
				else
					$linkRight = $resp['link'] ;
				$i = $i + 1 ;
			}
		echo '<p class = "disp">' . $linkLeft, $linkRight. '<p>' ;
	}			
	
	}
	
	echo "</root>" ;
?>