<?php

	$user = "root"; 
	$pwd = ""; 
	$risposta = "";
	
	$dbname = "bancaore";
	$hostname = "localhost";
	
	try{
		
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		/** CARICAMENTO CATEGORIE **/
		
		$stmt = "SELECT * FROM categorie";
		$categorie = array();
		$codici = array();
		foreach ($conn->query($stmt) as $row) {
			$codici[] = intval($row['codCategoria']);
			$categorie[] = ucfirst(strtolower($row['categoria']));
        }
		
		$ret = array("categorie"=>$categorie, "codici"=>$codici);
		
		echo json_encode($ret);
		
		$conn = null;
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
	
	
?>