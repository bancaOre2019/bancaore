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
		
		$email = $_POST['email'];
		$pwUt = $_POST['pw'];
		
		
		
		$stmt = $conn->prepare("SELECT pw
								  FROM utenti
								 WHERE email = :email");
				 
		$stmt->bindParam(':email', $email, PDO::PARAM_STR, 50);
		
		$stmt->execute();
		
		$resultPassword = $stmt->fetch();
		
		$pwHash = $resultPassword['pw'];
		
		
		if( password_verify($pwUt, $pwHash) ){
			$stmt = $conn->prepare("SELECT codiceUtente
										FROM utenti
									   WHERE email = :email");
				 
			$stmt->bindParam(':email', $email, PDO::PARAM_STR, 50);
			
			$stmt->execute();
			
			$result = $stmt->fetch();
			
			/*Prelevo l'identificativo dell'utente */
			$cod = $result['codiceUtente'];
			/* Effettuo il controllo */
			if ($cod == NULL) {
				$trovato = 0 ;
			} else {
				$trovato = 1; 
			}
			
			/* Username e password corrette */
			if($trovato === 1) {
				/* Registro il codice dell'utente */
				$_SESSION['cod'] = $cod;
			}
			
		} else {
			$trovato = 0;
		}
		
		$dati = array("trovato"=>$trovato);
		
		echo json_encode($dati);
		$conn = null;
		
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 

?>

