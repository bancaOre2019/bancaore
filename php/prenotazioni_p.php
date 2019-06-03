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
				
		$codUt = $_SESSION['cod'];
		
		/** NOME + FOTO **/
		
		$stmt = $conn->prepare("SELECT nome, cognome, foto
								  FROM utenti
								 WHERE codiceUtente = :cod");
				 
		$stmt->bindParam(':cod', $codUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		$nome = $result['nome'] . " " . $result['cognome'];
		$foto = "/bancaore/img/utenti/" . $result['foto'];
		
		/** LE PRENOTAZIONI SUI MIEI ANNUNCI **/
		
		$stmt = $conn->prepare("SELECT annunci.codAnnuncio, annunci.titolo, utenti.nome, utenti.cognome
								  FROM annunci JOIN utenti ON (annunci.committente = utenti.codiceUtente)
											   JOIN prenotazioni ON (annunci.codAnnuncio = prenotazioni.codAnnuncio)
								 WHERE annunci.committente = :cod AND annunci.LCT = 'L' AND prenotazioni.conferma = 0");
				 
		$stmt->bindParam(':cod', $codUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$prenotazioniSuiMiei = array();
		
		foreach($result as $row){
			$miei['titolo'] = $row['titolo'];
			$miei['codice'] =  $row['codAnnuncio'];
			$miei['nome'] =  $row['nome'] . " " . $row['cognome'];
			
			$prenotazioniSuiMiei[] = $miei;
		}
		
		/** -------------------------------------------------------- **/
		
		/** LE MIE PRENOTAZIONI **/
		
		$stmt = $conn->prepare("SELECT annunci.codAnnuncio, annunci.titolo, utenti.nome, utenti.cognome, prenotazioni.conferma
								  FROM annunci JOIN utenti ON (annunci.committente = utenti.codiceUtente)
											   JOIN prenotazioni ON (annunci.codAnnuncio = prenotazioni.codAnnuncio)
								 WHERE prenotazioni.codUtente = :cod");
				 
		$stmt->bindParam(':cod', $codUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$prenotazioniMie = array();
		
		foreach($result as $row){
			$fatte['titolo'] = $row['titolo'];
			$fatte['nome'] =  $row['nome'] . " " . $row['cognome'];
			
			if($row['conferma'] == 0){
				$fatte['conferma'] = "In via di conferma";
			} else if($row['conferma'] == 1) {
				$fatte['conferma'] = "Confermato";
			} else {
				$fatte['conferma'] = "Non definito";
			}
			
			$prenotazioniMie[] = $fatte;
		}
		
		$dati = array("nome"=>$nome, "foto"=>$foto, "prenotSuiMiei"=>$prenotazioniSuiMiei, "prenotMie"=>$prenotazioniMie);
		
		echo json_encode($dati);
		$conn = null;
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
	
?>