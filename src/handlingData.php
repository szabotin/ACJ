<?php // If click, will add 1 to the score of the item. 
	echo coucou ;					
	if(isset($_POST['b1'])){
		echo '<p class = "disp">' . $oldlL . '<p>' ;
		// getting the score
		$getInfo = $bdd->prepare('SELECT score FROM images WHERE link=?') ; 
		$getInfo->execute(array($oldlL)) ;
		$info = $getInfo->fetch() ;
								
								
		// increment the score
		$req = $bdd->prepare('UPDATE images SET score = :s WHERE link = :l');
		$req->execute(array('s' => $info['score']+1,
							'l' => $oldlL)) ;
		echo $info['score'] ;
	}
	$oldlL = $linkLeft ;

// If click, will add 1 to the score of the item. 
	if(isset($_POST['b2'])){
		echo '<p class = "disp">' . $oldlR . '<p>' ;
		// getting the score
		$getInfo = $bdd->prepare('SELECT score FROM images WHERE link=?') ; 
		$getInfo->execute(array($oldlR)) ;
		$info = $getInfo->fetch() ; 
				
		// increment the score
		$req = $bdd->prepare('UPDATE images SET score = :s WHERE link = :l');
		$req->execute(array('s' => $info['score'] + 1,
							'l' => $oldlR)) ;
		echo $info['score'] ;
	}
	$oldlR = $linkRight ;
?>