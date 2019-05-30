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
	
	if ($_POST['mode'] == 'random') { // send the links which are less compared and with the nearest score (obliged to have an even number of items)
		$req = $bdd->query('SELECT link, score 
							FROM images 
							WHERE rounds=(SELECT MIN(rounds) 
							   			  FROM images) 
							ORDER BY RAND() 
							LIMIT 0, 2') ;
		
		while($resp = $req->fetch()) {
			echo $resp['link'] . " " ;
		}
	}
	
	else if ($_POST['mode'] == 'score') { // send the links which are less compared and with the nearest score (obliged to have an even number of items)
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
	
	else if ($_POST['mode'] == 'percOfWin') {
		
		// select the comparaisons which have less than
		
		$req = $bdd->prepare('SELECT id_mat, id_it1, id_it2
							FROM results_matrix
							WHERE no_comp > 0
							AND no_win/no_comp < ?
							AND no_win/no_comp > ?
							ORDER BY RAND()
							LIMIT 0, 1') ;
		
		$req->execute(array($_POST['threshold'], 1 - $_POST['threshold'])) ;
		
		$id = array() ;
		
		while($resp = $req->fetch()) {
			$id[] = $resp['id_it1'] . " ";
			$id[] = $resp['id_it2'] ;
		}

		if ($id == []) {
			echo "finish" ;
		} 
		else {
			$req = $bdd->prepare('SELECT link
							FROM images
							WHERE id_item = ? OR id_item = ?') ;
		
			$req->execute(array($id[0], $id[1])) ;

			while($resp = $req->fetch()) {
				echo $resp['link'] . " " ;
			}
		}
	}
	
?>