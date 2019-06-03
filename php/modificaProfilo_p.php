<?php
	
	session_start();
	
	$user = "root"; 
	$pwd = ""; 
	$risposta = "";
	
	$dbname = "bancaore";
	$hostname = "localhost";
	
	try{
		
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$codiceUt = $_SESSION['cod'];
		$errore = 2;
		
		/* Per prima cosa prendo tutti i dati dell'utente.
		   In modo tale da fare una UPDATE completa e inserendo gli stessi valori nel caso in cui l'utente ne abbia modificato solo uno.
		   Senza fare questa procedura avrei poi la tupla piena di null.
		*/
		
		/* Prendo i dati e li inserisco nelle rispettive variabili */
		$stmt = $conn->prepare("SELECT nomeUtente, telefono, email, indirizzo, paese, cap, bio
								  FROM utenti
								 WHERE codiceUtente = :cod");
				 
		$stmt->bindParam(':cod', $codiceUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		// valori vecchi DB
		$cf = $result['nomeUtente'];
		$email = $result['email'];
		$telefono = $result['telefono'];
		$indirizzo = $result['indirizzo'];
		$paese = $result['paese'];
		$cap = $result['cap'];
		$bio = $result['bio'];
		// categoria
		
		/* A questo punto controllo i valori inseriti dall'utente se sono null */
		
		if(empty($_POST['mod_bio'])){
			$_POST['mod_bio'] = $bio;
		}
		
		if(empty($_POST['mod_telefono'])){
			$_POST['mod_telefono'] = $telefono;
		}
		
		if(empty($_POST['mod_indirizzo'])){
			$_POST['mod_indirizzo'] = $indirizzo;
		}
		
		if(empty($_POST['mod_paese'])){
			$_POST['mod_paese'] = $paese;
		}
		
		if(empty($_POST['mod_cap'])){
			$_POST['mod_cap'] = $cap;
		}
		
		if(empty($_POST['mod_email'])){
			$_POST['mod_email'] = $email;
		}
		
		if ($_FILES['fileupload']['size'] == 0 && $_FILES['fileupload']['error'] == 0){
			$stmt = $conn->prepare("UPDATE utenti
						   SET telefono = :tel, email = :em, indirizzo = :ind, paese = :pa, cap = :cap, bio = :bio
						 WHERE codiceUtente = :cod");

			$stmt->bindParam(':tel', $_POST['mod_telefono'], PDO::PARAM_STR, 10);
			$stmt->bindParam(':em', $_POST['mod_email'], PDO::PARAM_STR, 50);
			$stmt->bindParam(':ind', $_POST['mod_indirizzo'], PDO::PARAM_STR, 60);
			$stmt->bindParam(':pa', $_POST['mod_paese'], PDO::PARAM_STR, 45);
			$stmt->bindParam(':cap', $_POST['mod_cap'], PDO::PARAM_STR, 5);
			$stmt->bindParam(':bio', $_POST['mod_bio'], PDO::PARAM_LOB);
			$stmt->bindParam(':cod', $codiceUt, PDO::PARAM_INT);
			
			$stmt->execute();
			
			
		} else {
			
			if(isset($_FILES["fileupload"]["type"])) {
				$validextensions = array("jpeg", "jpg", "png", "JPEG", "JPG", "PNG");
				$temporary = explode(".", $_FILES["fileupload"]["name"]);
				$file_extension = end($temporary);
				
				if ((($_FILES["fileupload"]["type"] == "image/png") || ($_FILES["fileupload"]["type"] == "image/jpg") || ($_FILES["fileupload"]["type"] == "image/jpeg")
					) && ($_FILES["fileupload"]["size"] < 1024000)//Approx. 100kb files can be uploaded.
					&& in_array($file_extension, $validextensions)) {
					
					if ($_FILES["fileupload"]["error"] > 0) {
						echo "Return Code: " . $_FILES["fileupload"]["error"] . "<br/><br/>";
					} else {
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/bancaore/img/utenti/" . $_FILES["fileupload"]["name"])) {
							echo $_FILES["fileupload"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
						} else {
							$sourcePath = $_FILES['fileupload']['tmp_name']; // Storing source path of the file in a variable
							$targetPath = $_SERVER['DOCUMENT_ROOT'] . "/bancaore/img/utenti/" . $_FILES['fileupload']['name']; // Target path where file is to be stored
							move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
							/*echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
							echo "<br/><b>File Name:</b> " . $_FILES["file"]["name"] . "<br>";
							echo "<b>Type:</b> " . $_FILES["file"]["type"] . "<br>";
							echo "<b>Size:</b> " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
							echo "<b>Temp file:</b> " . $_FILES["file"]["tmp_name"] . "<br>";*/
							
							$newName = $cf . "." . $file_extension;
							rename($targetPath, $_SERVER['DOCUMENT_ROOT'] . "/bancaore/img/utenti/" . $cf . "." . $file_extension);
						}
					}
					
					$stmt = $conn->prepare("UPDATE utenti
						   SET telefono = :tel, email = :em, indirizzo = :ind, paese = :pa, cap = :cap, bio = :bio, foto = :foto
						 WHERE codiceUtente = :cod");

					$stmt->bindParam(':tel', $_POST['telefono'], PDO::PARAM_STR, 10);
					$stmt->bindParam(':em', $_POST['email'], PDO::PARAM_STR, 50);
					$stmt->bindParam(':ind', $_POST['indirizzo'], PDO::PARAM_STR, 60);
					$stmt->bindParam(':pa', $_POST['paese'], PDO::PARAM_STR, 45);
					$stmt->bindParam(':cap', $_POST['cap'], PDO::PARAM_STR, 5);
					$stmt->bindParam(':bio', $_POST['bio'], PDO::PARAM_LOB);
					$stmt->bindParam(':foto', $newName, PDO::PARAM_STR);
					$stmt->bindParam(':cod', $codiceUt, PDO::PARAM_INT);
					
					$stmt->execute();
				} else {
					echo "<span id='invalid'>***Invalid file Size or Type***<span>";
				}
			}
			
		}
		
			
		
		
	} catch(PDOException $ex){
	   echo "Errore connessione: " . $ex->getMessage();
	} 
	
	
	
?>