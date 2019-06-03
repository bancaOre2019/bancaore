<?php
		
	$user = "root"; 
	$pwd = ""; 
	$risposta = "";
	
	$dbname = "bancaore";
	$hostname = "localhost";
	
	try{
		
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		// LuogoResidenza === Paese
		
		$nome = ucfirst(strtolower($_POST['nome']));
		$cognome = ucfirst(strtolower($_POST['cognome']));
		$dataN = $_POST['dataNascita'];
		$luogoResidenza = ucfirst(strtolower($_POST['luogoResidenza']));
		$telefono = $_POST['telefono'];
		$indirizzo = ucwords(strtolower($_POST['indirizzo']));
		$cap = $_POST['cap'];
		$cf = strtoupper($_POST['cf']);
		$bio = $_POST['bio'];
		$email = strtolower($_POST['email']);
		$pw = $_POST['pw'];
		$codCategoria = intval($_POST['categoria']);
		
		if(isset($_FILES["fileupload"]["type"])) {
			$validextensions = array("jpeg", "jpg", "png", "JPEG", "JPG", "PNG");
			$temporary = explode(".", $_FILES["fileupload"]["name"]);
			$file_extension = end($temporary);
			
			if ((($_FILES["fileupload"]["type"] == "image/png") || ($_FILES["fileupload"]["type"] == "image/jpg") || ($_FILES["fileupload"]["type"] == "image/jpeg")
				) && ($_FILES["fileupload"]["size"] < 512000)//Approx. 100kb files can be uploaded.
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
			} else {
				echo "<span id='invalid'>***Invalid file Size or Type***<span>";
			}
		}
		
		$pwHash = password_hash($pw, PASSWORD_DEFAULT);
		
		$stmt = $conn->prepare("INSERT INTO utenti(nomeUtente, pw, nome, cognome, dataNascita, telefono, email, indirizzo, paese, cap, bio, codiceCategoria, foto)
								     VALUES(:cf, :pw, :nome, :cognome, :dataN, :telefono, :email, :indirizzo, :paese, :cap, :bio, :codCat, :foto)");
			 
		$stmt->bindParam(':cf', $cf, PDO::PARAM_STR, 16);
		$stmt->bindParam(':pw', $pwHash, PDO::PARAM_STR, 128);
		$stmt->bindParam(':nome', $nome, PDO::PARAM_STR, 45);
		$stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR, 45);
		$stmt->bindParam(':dataN', $dataN, PDO::PARAM_STR);
		$stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR, 10);
		$stmt->bindParam(':email', $email, PDO::PARAM_STR, 50);
		$stmt->bindParam(':indirizzo', $indirizzo, PDO::PARAM_STR, 60);
		$stmt->bindParam(':paese', $luogoResidenza, PDO::PARAM_STR, 45);
		$stmt->bindParam(':cap', $cap, PDO::PARAM_STR, 5);
		$stmt->bindParam(':bio', $bio, PDO::PARAM_LOB);
		$stmt->bindParam(':codCat', $codCategoria, PDO::PARAM_INT);
		$stmt->bindParam(':foto', $newName, PDO::PARAM_STR, 25);
		
		$stmt->execute();
		echo json_encode($dataN);
		$conn = null;
		
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	}
	
?>