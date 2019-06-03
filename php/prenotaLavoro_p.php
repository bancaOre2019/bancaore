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
		
		$codiceAnnuncio = $_POST['codice'];
		
		date_default_timezone_set('Europe/Rome');
		$dataPrenotazione = date('Y-m-d', time());
		
		/** QUERY DI INSERIMENTO DELL'ANNUNCIO **/
		
		$stmt = $conn->prepare("INSERT INTO prenotazioni(codAnnuncio, codUtente, dataPrenotazione, conferma)
								     VALUES(:codAnn, :codUt, :dataPrenot, 0)");
								 
		$stmt->bindParam(':codAnn', $codiceAnnuncio, PDO::PARAM_INT);
		$stmt->bindParam(':codUt', $codiceUt, PDO::PARAM_INT);
		$stmt->bindParam(':dataPrenot', $dataPrenotazione, PDO::PARAM_STR);
		
		$stmt->execute();
		
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
		
?>