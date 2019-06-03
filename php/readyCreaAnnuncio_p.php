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
		
		$stmt = $conn->prepare("SELECT nome, cognome, foto
								  FROM utenti
								 WHERE codiceUtente = :cod");
				 
		$stmt->bindParam(':cod', $codiceUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		$nome = $result['nome'] . " " . $result['cognome'];
		$foto = "/bancaore/img/utenti/" . $result['foto'];
		
		/** CARICAMENTO CATEGORIE **/
		
		$stmt = "SELECT * FROM categorie";
		$categorie = array();
		$codici = array();
		foreach ($conn->query($stmt) as $row) {
			$codici[] = intval($row['codCategoria']);
			$categorie[] = ucfirst(strtolower($row['categoria']));
        }
		
		$ret = array("nome"=>$nome, "categorie"=>$categorie, "codici"=>$codici, "foto"=>$foto);
		
		echo json_encode($ret);
		$conn = null;
	} catch(PDOException $ex){
	   $risposta = "Errore connessione: ".$ex->getMessage();
	} 
	
	
?>