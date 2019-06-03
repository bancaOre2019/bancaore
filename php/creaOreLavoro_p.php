<?php

	session_start();
	
	$user = "root";
	$pw = "";
	$dbname = "bancaore";
	$hostname = "localhost";
	
	try{
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pw);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$codiceUt = $_SESSION['cod'];
		
		$ore = $_POST['ore']; 
		$lavoro = $_POST['lavoro'];
		$descr = $_POST['descr'];
		
		date_default_timezone_set('Europe/Rome');
		$data = date("Y-m-d");
		
		$stmt = $conn -> prepare("INSERT INTO registroore(codUtente, codAnnuncio, data, numeroOre, descrizione)
									   VALUES(:codUt, :codAn, :data, :nOre, :descriz)");
		
		$stmt->bindParam(':codUt', $codiceUt, PDO::PARAM_INT);
		$stmt->bindParam(':codAn', $lavoro, PDO::PARAM_INT);
		$stmt->bindParam(':data', $data, PDO::PARAM_STR);
		$stmt->bindParam(':nOre', $ore, PDO::PARAM_INT);
		$stmt->bindParam(':descriz', $descr, PDO::PARAM_LOB);
		
		$stmt->execute();
		
	} catch (PDOException $e) {
		echo "Errore: " . $e->getMessage();
	}


?>