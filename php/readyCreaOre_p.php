<?php
	session_start();
	
	$username = "root";
	$pw = "";
	$hostname = "localhost";
	$dbname = "bancaore";
	
	try{
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $pw);
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
		
		
		
		/* lavori */
		
		$stmt = "SELECT annunci.codAnnuncio AS cod, annunci.titolo AS tit
		           FROM annunci JOIN prenotazioni ON (annunci.codAnnuncio = prenotazioni.codAnnuncio)
				  WHERE prenotazioni.codUtente = $codiceUt AND conferma = 1";
		
		$codici = array();
		$lavori = array();
		
		foreach($conn->query($stmt) as $row){
			$codici[] = intval($row['cod']);
			$lavori[] = $row['tit'];
		}
		
		$return = array("codici"=>$codici, "lavori"=>$lavori, "nome"=>$nome, "foto"=>$foto);
		
		echo json_encode($return);
		
		$conn = null;
	} catch (PDOException $e){
		echo "Errore: " . $e->getMessage();
	}
	
?>