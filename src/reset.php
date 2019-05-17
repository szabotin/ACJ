<?php

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
	
	// return to zero
	
	if (isset($_POST['reset'])) {
		$req = $bdd->exec('UPDATE images SET score = 0, rounds = 0, acj_score = 0');
		$req = $bdd->exec('DELETE FROM results') ;
		$req = $bdd->exec('UPDATE results_matrix SET no_comp = 0, no_win = 0') ;	
	}
?>