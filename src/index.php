<!DOCTYPE html>
<?php
	try
	{
		$bdd = new PDO('mysql:host=localhost;dbname=ACJ;charset=utf8', 'root', '');
	}
	catch(Exception $e)
	{
			die('Erreur : '.$e->getMessage());
	}
?>

<html lang = "fr">
	<head>
		<title> Preferences </title>
		<meta charset = "utf-8" />
		<meta name="auteur" content = "SZABO Clement" />
		<link rel = "stylesheet" type = "text/css" media = "all" href = "../styles/Reset.css"/>
			<link rel = "stylesheet" type = "text/css" media = "screen" href = "../styles/GeneralStyle.css"/>
	</head>
	<?php //echo '<p class = "disp">' . $minRound['mini'] . '<p>' ; ?>
	<body>
		<header>
			<h1> Click on what you prefer </h1>
		</header>
		<main>
			<div class = "compareBox"> 
				<?php // get the link of 2 pictures by choosing among the ones which have the same number of comparisons and the same score				
					
					// return to zero
					$req = $bdd->exec('UPDATE images SET score = 0, rounds = 0');
					
					// get the number of picture
					$qnbpic = $bdd->query('SELECT COUNT(*) AS nbp FROM images') ; 
					$nbpic = $qnbpic->fetch()['nbp'] ;
					
					$linkRight = "" ;
					$linkLeft = "" ;
					
					/*$norounds = 3 ;
					$nbvicmin = 0 ;
					$nbvicmax = $nbpic - $nb ;*/
					
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
				?>
				<div class = "button" id = "boxLeft">
					<img src="<?php echo $linkLeft ?>" id = "itemLeft"> </img>
					<form method="POST" action="index.php" id="buttonLeft">
						<input type="submit" name="b1" value="This One !" id = "bL">																		
					</form>
				</div>
				
				<div id = "boxRight">
					<img src="<?php echo $linkRight ?>" id="itemRight"> </img>
					<form method="POST" action="index.php" id="buttonRight">
						<input type="submit" name="b2" value="This One !" id = "bR">
					</form>
				</div>
				
			<?php // Add one round to say that these two items have been compared
				if(isset($_POST['b1']) OR isset($_POST['b2'])){
					
					// getting the 2 links we want to increment the number of rounds
					$getInfo = $bdd->prepare('SELECT rounds, link FROM images WHERE link=? OR link=?') ; 
					$getInfo->execute(array($linkRight, $linkLeft)) ;
								
					while ($info = $getInfo->fetch()) { // just twice, because of two links
						$req = $bdd->prepare('UPDATE images SET rounds = :r WHERE link = :l');
						$req->execute(array('r' => $info['rounds'] + 1,
														'l' => $info['link'])) ;
						echo $info['rounds'] ;
					}
				}
			?>
			</div>
		</main>
		<script src="ACJ.js"></script>
	</body>
</html>