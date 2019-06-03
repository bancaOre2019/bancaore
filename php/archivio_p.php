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
		
		/** ANNUNCI ESEGUITI **/
		$stmt = $conn->prepare("SELECT annunci.codAnnuncio, titolo, utenti.codiceUtente, nome, cognome, descrizione, categorie.codCategoria, categoria, dataInizio, dataFine
								  FROM annunci JOIN utenti ON (annunci.committente = utenti.codiceUtente)
											   JOIN categorie ON (annunci.codCat = categorie.codCategoria)
											   JOIN prenotazioni ON (prenotazioni.codAnnuncio = annunci.codAnnuncio)
								 WHERE prenotazioni.codUtente = :cod AND annunci.LCT = 'T'
								 GROUP BY annunci.codAnnuncio, titolo, utenti.codiceUtente, nome, cognome, descrizione, categorie.codCategoria, categoria, dataInizio, dataFine");
				 
		$stmt->bindParam(':cod', $codUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$eseguiti = array();
		
		foreach($result as $row){
			
			$annuncioE['titolo'] = $row['titolo'];
			$annuncioE['nome'] = $row['nome'] . " " . $row['cognome'];
			$annuncioE['descrizione'] =  $row['descrizione'];
			$annuncioE['categoria'] =  $row['categoria'];
			$annuncioE['dataInizio'] =  date("d-m-Y", strtotime($row['dataInizio']));
			$annuncioE['dataFine'] =  date("d-m-Y", strtotime($row['dataFine']));
			
			$eseguiti[] = $annuncioE;
		}
		
		/** -------------------------------------------------------- **/
		
		/** ANNUNCI COMMISSIONATI **/
		
		$stmt = $conn->prepare("SELECT annunci.codAnnuncio, titolo, descrizione, categorie.codCategoria, categoria, dataInizio, dataFine
								  FROM annunci JOIN utenti ON (annunci.committente = utenti.codiceUtente)
											   JOIN categorie ON (annunci.codCat = categorie.codCategoria)
								 WHERE committente = :cod AND annunci.LCT = 'T'");
				 
		$stmt->bindParam(':cod', $codUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$commissionati = array();
		
		foreach($result as $row){
			$annuncioC['titolo'] = $row['titolo'];
			$annuncioC['descrizione'] =  $row['descrizione'];
			$annuncioC['categoria'] =  $row['categoria'];
			$annuncioC['dataInizio'] =  date("d-m-Y", strtotime($row['dataInizio']));
			$annuncioC['dataFine'] =  date("d-m-Y", strtotime($row['dataFine']));
			
			$commissionati[] = $annuncioC;
		}
		
		$dati = array("nome"=>$nome, "foto"=>$foto, "eseguiti"=>$eseguiti, "commissionati"=>$commissionati);
		
		echo json_encode($dati);
		$conn = null;
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
	
?>