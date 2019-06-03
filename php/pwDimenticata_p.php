<?php
	
	$user = "root"; 
	$pwd = ""; 
	$risposta = "";
	
	$dbname = "bancaore";
	$hostname = "localhost";
	
	try{
		
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$to_email = $_POST['email'];
		
		$stmt = $conn->prepare("SELECT nome
								  FROM utenti
								 WHERE email = :email");
				 
		$stmt->bindParam(':email', $to_email, PDO::PARAM_STR);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		if(!empty($result)){
			$trovato = 1;
			
			$to_name = $result['nome'];
			$subject = "Reset password per il tuo account della Banca Ore!";
			$headers = "From: banca.ore19@gmail.com";
			
			$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
			$pass = array(); //remember to declare $pass as an array
			$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
			for ($i = 0; $i < 10; $i++) {
				$n = rand(0, $alphaLength);
				$pass[] = $alphabet[$n];
			}
			
			$newPassword = implode($pass); //turn the array into a string
			
			$pwHash = password_hash($newPassword, PASSWORD_DEFAULT);
			
			$stmt = $conn->prepare("UPDATE utenti
									  SET pw = :pass
									 WHERE email = :email");
			
			$stmt->bindParam(':pass', $pwHash, PDO::PARAM_STR);
			$stmt->bindParam(':email', $to_email, PDO::PARAM_STR);
		
			$stmt->execute();
			
			$body = 
			'Ciao ' . $to_name . '! La tua password temporanea è: ' . $newPassword . '! Cambiala appena accederai di nuovo!';
				
			if ( mail($to_email, $subject, $body, $headers)) {
				echo("Email successfully sent to $to_email...");
			} else {
				echo("Email sending failed...");
			}
		} else {
			$trovato = 0;
		}
		
		echo $trovato;
		$conn = null;
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
	
?>