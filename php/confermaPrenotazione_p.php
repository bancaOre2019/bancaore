<?php

	$user = "root"; 
	$pwd = ""; 
	$risposta = "";
	
	$dbname = "bancaore";
	$hostname = "localhost";
	
	try{
		
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$codiceAnnuncio = intval($_POST['codice']);
		
		$stmt = $conn->prepare("UPDATE prenotazioni
								   SET conferma = 1
								 WHERE codAnnuncio = :cod");
				 
		$stmt->bindParam(':cod', $codiceAnnuncio, PDO::PARAM_INT);
		
		$stmt->execute();
		
		
		$conn = null;
	
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
	
?>