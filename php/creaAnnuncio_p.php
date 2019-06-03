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
		
		$codCategoria = $_POST['categoria'];
		$titolo = ucfirst(strtolower($_POST['titolo']));;
		$desc = $_POST['descr'];
		$lct = "L";
		date_default_timezone_set('Europe/Rome');
		$dataInizio = date('Y-m-d', time());
		
		/** QUERY DI INSERIMENTO DELL'ANNUNCIO **/
		
		$stmt = $conn->prepare("INSERT INTO annunci(committente, titolo, descrizione, codCat, LCT, dataInizio)
								     VALUES(:cod, :titolo, :descr, :cat, :flag, :dataInizio)");
								 
		$stmt->bindParam(':cod', $codiceUt, PDO::PARAM_INT);
		$stmt->bindParam(':titolo', $titolo, PDO::PARAM_STR, 45);
		$stmt->bindParam(':descr', $desc, PDO::PARAM_LOB);
		$stmt->bindParam(':cat', $codCategoria, PDO::PARAM_INT);
		$stmt->bindParam(':flag', $lct, PDO::PARAM_STR, 1);
		$stmt->bindParam(':dataInizio', $dataInizio, PDO::PARAM_STR);
		
		
		$stmt->execute();
		
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
		
?>