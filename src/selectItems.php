<?php

	/*************************/
	/*  connect to DataBase  */
	/*************************/
	
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
	
	/*****************************************************/
	/* select the next 2 items by the progressive method */
	/*****************************************************/
	
	
	// 	in this function, s is reset at each rounds
	
	/*function selectNewItems($dbase) {	
	
		// reset the weight
		
		$req = $dbase->query('UPDATE images SET weight = 0') ;
	
		// get the total number of items
	
		$req = $dbase->query('SELECT COUNT(*) FROM images') ;
		$nbItems = $req->fetch()[0] ; 
		
		// get the number of rounds already done
		
		$req = $dbase->query('SELECT MIN(rounds) FROM images') ;
		$nbRounds = $req->fetch()[0] ;
		
		// get number of items administred and remains to be administred in this round, and the ratio of items to be administred
		
		$req = $dbase->prepare('SELECT COUNT(*) FROM images WHERE rounds = ?') ;
		$req->execute(array($nbRounds)) ;
		
		$toBeAdmin = $req->fetch()[0] ; 
		$alrAdmin = $nbItems - $toBeAdmin ;
		$ratio = $alrAdmin / $nbItems ;
		
		// get all informations of the items which have to be administred
		
		$req = $dbase->prepare('SELECT id_item, information FROM images WHERE rounds = ?') ;
		$req->execute(array($nbRounds)) ;
		
		while ($info = $req->fetch()) {
			$itemIds[] = $info['id_item'] ;
			$informations[] = $info['information'] ;
		}
		
		$maxInfo = max($informations) ;
		
		// get the weight of each item to be administred, conform to the progressive method
		
		for ($i = 0 ; $i < $toBeAdmin ; $i++) {
			
			// get a random number between 0 and $maxInfo
			
			$acc = 1000000 ;
			$random = rand(0, $maxInfo * $acc) ;
			$random /= $acc ;	
			
			$weight = ((1-$ratio) * $random) + ($ratio * $informations[$i]) ; // formula from the progressive method
			
			// put the weight on the data base
			
			$req = $dbase->prepare('UPDATE images SET weight = ? WHERE id_item = ?') ;
			$req->execute(array($weight, $itemIds[$i])) ;
		}	
		
		$req = $dbase->query('SELECT link FROM images ORDER BY weight DESC LIMIT 2') ;
		
		while ($res = $req->fetch()) {
			$links[] = $res['link'] ;
		}
		
		
		return $links ;
	}*/
	
	// in this function, s is decreasing for each round
	
	/*********************************************************************/
	/* $a is the begenning of the interval of s.                         */
	/* $b is the endind of the interval of s.                            */
	/* $n is the number of rounds that s will take to grow from $a to $b */
	/*********************************************************************/
	
	function selectNewItems($dbase, $a, $b, $n) {	
	
		// reset the weight
		
		$req = $dbase->query('UPDATE images SET weight = 0') ;
	
		// get the total number of items
	
		$req = $dbase->query('SELECT COUNT(*) FROM images') ;
		$nbItems = $req->fetch()[0] ; 
		
		// get the number of rounds already done
		
		$req = $dbase->query('SELECT MIN(rounds) FROM images') ;
		$nbRounds = $req->fetch()[0] ;
		
		// get number of items administred and remains to be administred in this round, and the ratio of items to be administred
		
		$req = $dbase->prepare('SELECT COUNT(*) FROM images WHERE rounds = ?') ;
		$req->execute(array($nbRounds)) ;
		
		$toBeAdmin = $req->fetch()[0] ; 
		$alrAdmin = $nbItems - $toBeAdmin ;
		
		$ratio = $a + ($nbRounds / $n)  ;
		
		if ($ratio > $b) {
			$ratio = $b ;
		}
		
		// get all informations of the items which have to be administred
		
		$req = $dbase->prepare('SELECT id_item, information FROM images WHERE rounds = ?') ;
		$req->execute(array($nbRounds)) ;
		
		while ($info = $req->fetch()) {
			$itemIds[] = $info['id_item'] ;
			$informations[] = $info['information'] ;
		}
		
		$maxInfo = max($informations) ;
		
		// get the weight of each item to be administred, conform to the progressive method
		
		for ($i = 0 ; $i < $toBeAdmin ; $i++) {
			
			// get a random number between 0 and $maxInfo
			
			$acc = 1000000 ;
			$random = rand(0, $maxInfo * $acc) ;
			$random /= $acc ;	
			
			$weight = ((1-$ratio) * $random) + ($ratio * $informations[$i]) ; // formula from the progressive method
			
			// put the weight on the data base
			
			$req = $dbase->prepare('UPDATE images SET weight = ? WHERE id_item = ?') ;
			$req->execute(array($weight, $itemIds[$i])) ;
		}	
		
		$req = $dbase->query('SELECT link FROM images ORDER BY weight DESC LIMIT 2') ;
		
		while ($res = $req->fetch()) {
			$links[] = $res['link'] ;
		}
		
		$links[] = " s : " . $ratio ;
		
		
		return $links ;
	}
	
	function selectNewItemsRandom() { // send the links which are less compared and with the nearest score (obliged to have an even number of items)
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
	
	function selectNewItemsByScore() { // send the links which are less compared and with the nearest score (obliged to have an even number of items)
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
	
	function selectNewItemsByNumberOfWins($threshold) {
		
		// select the comparaisons which have less than
		
		$req = $bdd->prepare('SELECT id_mat, id_it1, id_it2
							FROM results_matrix
							WHERE no_comp > 0
							AND no_win/no_comp < ?
							AND no_win/no_comp > ?
							ORDER BY RAND()
							LIMIT 0, 1') ;
		
		$req->execute(array($threshold, 1 - $threshold)) ;
		
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
	
	if (isset($_POST['PM'])) {
		$itemsSelected = selectNewItems($bdd, 0, 0.85, 50) ;
		echo $itemsSelected[0] . " " . $itemsSelected[1] ;
		echo $itemsSelected[2] ;
	}
?>