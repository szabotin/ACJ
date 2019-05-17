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
	
	/**************/
	/*  functions */
	/**************/
	
	function fill_example($dbase) {
		$mat[] = 35 ;
		$mat[] = 32 ;
		$mat[] = 30 ;
		$mat[] = 19 ;
		$mat[] = 20 ;
		$mat[] = 30 ;
		$mat[] = 18 ;
		$mat[] = 40 ;
		$mat[] = 20 ;
		$mat[] = 40 ;
		$mat[] = 25 ;
		$mat[] = 27 ;
		$mat[] = 35 ;
		$mat[] = 30 ;
		$mat[] = 36 ;
		
		for ($i = 0 ; $i < 15 ; $i++) {
			$req = $dbase->prepare('UPDATE results_matrix SET no_comp = ? WHERE id_mat = ?') ;
			$req->execute(array($mat[$i], $i+1)) ;
		}
		
		$matr[] = 1.052 ;
		$matr[] = 0.704 ;
		$matr[] = 0.318 ;
		$matr[] = -0.441 ;
		$matr[] = -0.624 ;
		$matr[] = -1.009 ;
		
		for ($i = 0 ; $i < 6 ; $i++) {
			$req = $dbase->prepare('UPDATE images SET acj_score = ? WHERE id_item = ?') ;
			$req->execute(array($matr[$i], $i+1)) ;
		}
		
		$matrx[] = 106 ;
		$matrx[] = 98 ;
		$matrx[] = 91 ;
		$matrx[] = 62 ;
		$matrx[] = 49 ;
		$matrx[] = 31 ;
		
		for ($i = 0 ; $i < 6 ; $i++) {
			$req = $dbase->prepare('UPDATE images SET score = ? WHERE id_item = ?') ;
			$req->execute(array($matrx[$i], $i+1)) ;
		}
		
	}
	
	function getProb ($a, $b) { 
	
		$diff = $a - $b ;
		
		// Use the formula to get the probability for $a to win against b
		
		return exp($diff) / (1 + exp($diff)) ;
	}
	
	// compute the standard deviation of an array
	
	function standardDeviation ($arr, $len) {
		$sumSqDeviation = 0 ;

		for ($i = 0 ; $i < $len ; $i++) {
			$sumSqDeviation += $arr[$i] * $arr[$i] ;
		}
		
		$sumSqDeviation /= $len - 1 ;
		$res = sqrt($sumSqDeviation) ;
		
		return $res ;
	}
	
	function computeACJ($dbase) {
		//fill_example($dbase) ;
		
		// matrix and arrays declarations
		
		// arrays for ACJ round computing
		
		$matRounds = array() ; // matrix of Rounds for each comparisons
		$matWins = array() ; // matrix of Wins for each comparisons
		$matProb = array() ; // matrix of probabilities of win
		$expectedScore = array() ;
		$informations = array() ;
		$updatedScores = array() ;
		$zeroMeanScore = array() ;
		
		// arrays for reliability computing
		
		$standardError = array() ;
		$roundDiff = array() ; // the difference between the old and the new estimation
		
		$threshold = 0.95 ; // threshold whose ca estimate that ACJ is enough accurate to stop it
		
		/**********************************/
		/* get informations from database */
		/*        to compute ACJ          */
		/**********************************/
		
		// get the ids and acj_scores
		
		$req = $dbase->query('SELECT id_item, score, acj_score FROM images') ;
		
		while($infos = $req->fetch()) {
			$itemIds[] = $infos['id_item'] ;
			$scores[] = $infos['score'] ;
			$acjScores[] = $infos['acj_score'] ;
		}
		
		$nbItems = count($itemIds) ; // get the number of items in order to avoid to recompute it at each loop
		
		// get the matrix of rounds.
		// -> each cell represents the number of rounds between the row and column id item 
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			$matRounds[] = array() ;
			$matWins[] = array() ;
			
			$req = $dbase->prepare('SELECT no_comp, no_win FROM results_matrix WHERE id_it1 = ? OR id_it2 = ?') ;
			$req->execute(array($itemIds[$i],$itemIds[$i])) ;
			
			$j = 0 ;
			while($infos = $req->fetch()) {
				if ($i == $j) {
					$matRounds[$i][] = 0 ;
				}
				$matRounds[$i][] = $infos['no_comp'] ;
				$j++ ;
			}
			$matRounds[$i][] = 0 ;
		}
		/*for ($i = 0 ; $i < $nbItems ; $i++) {
			for ($j = 0 ; $j < $nbItems ; $j++) {
				echo '[' . $matWins[$i][$j] . "] ";
			}
			echo "\n" ;
		}*/
		
		/***************/
		/* compute ACJ */
		/***************/
		
		// fisrt, we have to compute the probabilities of win for each item between each other
		// we use the formula : exp(a-b) / 1 + exp(a-b) where a and b are the current estimation scores
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			$matProb[] = array() ;
			for ($j = 0 ; $j < $nbItems ; $j++) {
				if ($i != $j) {
					$matProb[$i][] = getProb($acjScores[$i], $acjScores[$j]) ;
				}
				else {
					$matProb[$i][] = 0 ;
				}
			}
		}
		
		// get the expected score
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			
			// expected score is the sum of product of the differents probabilities to win and the number of rounds
			
			$sumproduct = 0 ;
			for ($j = 0 ; $j < $nbItems ; $j++) {
				$prob = $matProb[$i][$j] ;
				$sumproduct += $prob * $matRounds[$i][$j] ;
			}
			$expectedScore[] = $sumproduct ;
		}
		
		// this computes the sum of products, like expected score, but we add also the probability of loose
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			$sumproduct = 0 ;
			for ($j = 0 ; $j < $nbItems ; $j++) {
				$prob = $matProb[$i][$j] ;
				$sumproduct += $prob * (1-$prob) * $matRounds[$i][$j] ;
			}
			$informations[] = $sumproduct ;
		}
		
		// get the updated score, without zeroMeansquare
		
		$m = 0 ; // the mean of $updatedScores
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			// updated score is obtain by this formula : oldEstimation + (realScore - expected score) / sumproduct of probs of win, probs of lose and number of comparaisons
			$updatedScores[] = $acjScores[$i] + ($scores[$i]-$expectedScore[$i])/$informations[$i] ;
			$m += $updatedScores[$i] ; // we want to have the mean in the same time
		}
		
		$m /= $nbItems ; // will be useful for the zero mean score
		
		// get the real updatedScore with zero Mean
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			$zeroMeanScore[] = $updatedScores[$i] - $m ;
		}
		
		/***********************/
		/* update the database */
		/***********************/
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			$req = $dbase->prepare('UPDATE images SET acj_score = ? WHERE id_item = ?') ;
			$req->execute(array($zeroMeanScore[$i], $itemIds[$i])) ;
		}
		
		/***************************/
		/* compute the reliability */
		/***************************/
		
		// get the standard error of the estimations
		
		/*for ($i = 0 ; $i < $nbItems ; $i++) {
			$standardError[] = 1/sqrt($informations[$i]) ;
		}*/
		
		// get the standard deviation of the new estimation scores
		
		$obsStandardDeviation = standardDeviation($zeroMeanScore, $nbItems) ;
		 
		// get the difference between the estimation of 2 rounds
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			$diff = $updatedScores[$i] - $acjScores[$i] ;
			$roundDiff[] = $diff * $diff ;
		}
		
		// datas useful for computing the reliability. 
		
		$mse = array_sum($roundDiff) / $nbItems ; // mean of the $roundDiff array
		$rmse = sqrt($mse) ; // square root of mse -> RootMeanSquare
		$trueSD = sqrt($obsStandardDeviation * $obsStandardDeviation - $mse) ;
		$sepCoeff = $trueSD / $rmse ; // separation Coefficient
		$sqSepCoeff = $sepCoeff * $sepCoeff ; // square of separation coefficient
		
		$reliability = $sqSepCoeff / (1 + $sqSepCoeff) ;
		
		/*for ($i = 0 ; $i < $nbItems ; $i++) {
			echo $roundDiff[$i] . "\n" ;
		}*/
		
		if ($reliability > $threshold) {
			echo "too accurate ! Can stop " ;
		}
		else {
			echo "Let's continue !" ;
		}
		echo "Reliability : " . $reliability . "\n" ;
		
		// get the matrix of wins between for a against b
		
		
		/*$residual = 
		
		for ($i = 0 ; $i < $nbItems ; $i++) {
			for ($j = 0 ; $j < $nbItems ; $j++) {
				
			}
		}*/
	}
	
	if (isset($_POST['compute'])) {
		computeACJ($bdd) ;
	}
?>