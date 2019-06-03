<?php
	
	
	$user = "root"; 
	$pwd = ""; 
	$risposta = "";
	
	$dbname = "bancaore";
	$hostname = "localhost";
	
	try{
		
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$codCat = $_POST['categoria'];
		
		$stmt = $conn->prepare("SELECT annunci.codAnnuncio, annunci.titolo, annunci.descrizione, utenti.codiceUtente, utenti.nome, utenti.cognome, utenti.foto 
								  FROM annunci JOIN utenti ON (annunci.committente = utenti.codiceUtente)
								 WHERE annunci.LCT = 'L' AND annunci.codCat = :cod");
				 
		$stmt->bindParam(':cod', $codCat, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$annunci = array();
		$nomi = array();
		
		foreach($result as $row){
			$annunci[] = $row['codAnnuncio'] . "-" . $row['titolo'] . "-" . $row['descrizione'];
			$nomi[] = $row['codiceUtente'] . "-" . $row['nome'] . " " . $row['cognome'] . "-" . "/bancaore/img/utenti/" . $row['foto'];
		}
		
		
		$ret = array("annunci"=>$annunci, "nomi"=>$nomi);
		
		echo json_encode($ret);
		
		$conn = null;
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
	
	
?>