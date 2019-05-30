<?php 
	header("Content-Type: text/plain") ;
	
	/*****************************/
	/*  connect to DataBase      */
	/*****************************/
	
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
	
	/**************/
	/*  functions */
	/**************/
	
	// transform the absolute path to relative path because the link is relative in dataBase
	
	function absToRel($absPath, $rootAbs, $rootRel) {
		$path = explode($rootAbs, $absPath)[1] ; // get the path
		$rel = $rootRel . $path ; // add the relative path
		
		return $rel ;
	}
	
	// return the id of a path
	
	function getId($path1, $dbase){
		$req = $dbase->prepare('SELECT id_item FROM images WHERE link=?') ; 
		$req->execute(array($path1)) ;
		
		$info = $req->fetch() ;
		
		return $info['id_item'] ;
	}

	/*********************************************/
	/*  functions which makes link with database */
	/*********************************************/

	/***************************/
	/*  deal with images table */
	/***************************/
	
	function incrementScore($link, $dbase) {
		// getting the score
		$req = $dbase->prepare('SELECT score, link FROM images WHERE link = ?') ; 
		$req->execute(array($link)) ;
		
		$info = $req->fetch() ;
									
		// increment the score
		$req = $dbase->prepare('UPDATE images SET score = :s WHERE link = :l');
		$req->execute(array('s' => $info['score']+1,
							'l' => $link)) ;
	}
	
	function incrementRound($link1, $link2, $dbase) {
		
		echo $link1 . $link2 ;
		
		// getting the 2 links we want to increment the number of rounds
		$req = $dbase->prepare('SELECT rounds, link FROM images WHERE link=? OR link=?') ; 
		$req->execute(array($link1, $link2)) ;
			
		//echo $req->fetch()[0] ;
		while ($info = $req->fetch()) { // just twice, because of two links
			$requ = $dbase->prepare('UPDATE images SET rounds = :r WHERE link = :l');
			$requ->execute(array('r' => $info['rounds'] + 1,
								'l' => $info['link'])) ;
		}
	}
	
	/************************************************/
	/*  function addMatch will add the match in the */
	/*       results and results_matrix table       */
	/************************************************/

	function addMatch($idL, $idR, $winner, $dbase) {
		
		// add the match into the results database
		
		$req = $dbase->prepare('INSERT INTO results(id_it1, id_it2, win) VALUES(:id_it1, :id_it2, :winner)');
		
		if ($winner == $idL) { // if itemLeft won
			$req->execute(array(
				'id_it1' => $idL,
				'id_it2' => $idR,
				'winner' => 1
				));
		}
		
		else { // if itemRight won
			$req->execute(array(
				'id_it1' => $idL,
				'id_it2' => $idR,
				'winner' => 0
				));
		}
		
		// add the match into the results_matrix database
		
		$req = $dbase->prepare('SELECT id_mat, no_comp, no_win FROM results_matrix WHERE id_it1=? AND id_it2=?') ; 
		$req->execute(array($idL, $idR)) ;
		
		// for example : the items were diplayed with 3 in the left, and 2 in the right
		//	We will search for the match "3 against 2" , but it could be save din the database as "2 against 3"
		// so the query above see if 3 against 2 exists
		
		// -> if the match 3 against 2 exists
		
		if ($info = $req->fetch()) { 
			$req = $dbase->prepare('UPDATE results_matrix SET no_comp = :n, no_win = :w WHERE id_mat=:id');
			
			if ($winner == $idL) { // that means that 3 won 
				$req->execute(array('n' => $info['no_comp'] + 1,
									'w' => $info['no_win'] + 1,
									'id' => $info['id_mat']	
								)) ;
				echo $winner . "gauche !" ;
			}
			
			else { // that means that 2 won
				$req->execute(array('n' => $info['no_comp'] + 1,
									'w' => $info['no_win'],
									'id' => $info['id_mat']
								)) ;
				echo $winner . "droite !" ;
			}
		}
		
		// -> if the match 2 against 3 exists, instead of 3 against 2
		
		else { 
			// we execute the query with the other order
			$req = $dbase->prepare('SELECT id_mat, no_comp, no_win FROM results_matrix WHERE id_it1=? AND id_it2=?') ; 
			$req->execute(array($idR, $idL)) ;
			
			// here, with the example above, we are searching "2 against 3", because "3 against 2" doesn't exist
			
			$info = $req->fetch() ;
			$req = $dbase->prepare('UPDATE results_matrix SET no_comp = :n, no_win= :w WHERE id_mat=:id');
			
			if ($winner == $idR) { // that means that 3 won
				$req->execute(array('n' => $info['no_comp'] + 1,
									'w' => $info['no_win'] + 1,
									'id' => $info['id_mat']
									
								)) ;
				echo $winner . "envers droite !" ;
			}
			
			else { // that means that 2 won
				$req->execute(array('n' => $info['no_comp'] + 1,
									'w' => $info['no_win'],
									'id' => $info['id_mat']
									
								)) ;
				echo $winner . "envers gauche !" ;
			}
		}
	}
	
	/***********************************************/
	/*       treatment depends on data send by     */ 
	/*	XMLHttprequest in the differents functions */
	/*                 in ACJ.js file              */
	/***********************************************/
	
	if(isset($_POST['incrementS'])){
		// transform to relative path
		$relPath = absToRel($_POST['incrementS'],"http://localhost/ACJ/implementation/", "../") ;
		
		incrementScore($relPath, $bdd) ;
	}
	
	if (isset($_POST['l1']) and isset($_POST['l2'])) {
		
		$relPathL = absToRel($_POST['l1'],"http://localhost/ACJ/implementation/", "../") ;
		$relPathR = absToRel($_POST['l2'],"http://localhost/ACJ/implementation/", "../") ;
		
		$idL = getId($relPathL, $bdd) ;
		$idR = getId($relPathR, $bdd) ;
		
		if (isset($_POST['win'])) {
			$relPathWin = absToRel($_POST['win'],"http://localhost/ACJ/implementation/", "../") ;
			$idWin = getId($relPathWin, $bdd) ;
			
			addMatch($idL, $idR, $idWin, $bdd) ;
		}
		
		else {
			incrementRound($relPathL, $relPathR, $bdd) ;
		}
	}
	
	if (isset($_POST['initRM'])) {
		$req = $bdd->query('DELETE FROM results_matrix') ;
		$req = $bdd->query('SELECT id_item FROM images');
		
		$ids = array() ;
		
		while($resp = $req->fetch()) {
			$ids[] = $resp['id_item'] ;
		}	
		
		$nbVal = count($ids) ;
		echo $nbVal ;
		for ($i= 0 ; $i < $nbVal ; $i++) {
			for($j = $i+1 ; $j < $nbVal ; $j++) {
				$req = $bdd->prepare('INSERT INTO results_matrix(id_it1, id_it2, no_comp, no_win) VALUES(:id1, :id2, 0, 0)') ; 
				$req->execute(array('id1' => $ids[$i],
									'id2' => $ids[$j])) ;
				echo $i . "-" . $j . "\n" ;
			}
		}		
	}
?>