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
		
		$codiceUt =  $_SESSION['cod'];
		
		$stmt = $conn->prepare("SELECT nome, cognome, foto
								  FROM utenti
								 WHERE codiceUtente = :cod");
				 
		$stmt->bindParam(':cod', $codiceUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		$nome = $result['nome'] . " " . $result['cognome'];
		$foto = "/bancaore/img/utenti/" . $result['foto'];		
		
		/*query per il prelevamento dei dati inerenti ai lavori*/
		
		$stmt = $conn->prepare("SELECT titolo,nome,cognome,descrizione,categoria,dataConferma, 
									FROM prenotazioni JOIN utenti ON prenotazioni.codEsecutore= utenti.codiceUtente
										JOIN annunci ON prenotazioni.codAnnuncio=annunci.codAnnuncio
										JOIN categorie ON annunci.codCat=categorie.codCategoria
									WHERE codEsecutore= :cod AND LCT = 'C'");
		
		$stmt->execute();		
		
		$result = $stmt->fetchAll();
		
		$tabellaPrima  = array(); // salvati nel formato "titolo-nomeEseguo-dataEseguo-descrizioneEseguo-categoriaEseguo-dataConferma"
		
		foreach($result as $row){
			$tabellaPrima[] = $row['titolo'] . "-" . $row['nome'] . "-" . $row['cognome']. "-" . $row['descrizione']. "-" . $row['categoria']. "-" . $row['dataConferma'];
			

		}
		$stmt = $conn->prepare("SELECT titolo,annunci.descrizione,categoria,dataConferma,SUM(registroore.numeroore) AS contoOre
								FROM annunci 
                                			 JOIN prenotazioni ON prenotazioni.codAnnuncio=annunci.codAnnuncio
											 JOIN utenti ON committente= utenti.codiceUtente
										     JOIN categorie ON annunci.codCat=categorie.codCategoria
											 JOIN registroore ON annunci.codAnnuncio = registroore.codAnnuncio
								WHERE codiceUtente=:cod AND LCT = 'C'
                                GROUP BY titolo,annunci.descrizione,categoria,dataConferma");
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$tabellaSeconda  = array(); // salvati nel formato "titolo-descrizione-categoria-dataConferma-contoOre"
		
		foreach($result as $row){
			$tabellaSeconda[] = $row['titolo'] . "-" . $row['descrizione'] . "-" . $row['categoria']. "-" . $row['dataConferma']. "-" . $row['contoOre'];
		}
		
		$ret = array("tabellaPrima"=>$tabellaPrima,"tabellaSeconda"=>$tabellaSeconda,"nome"=>$nome, "foto"=>$foto);
		
		echo json_encode($ret);
		
		$conn = null;
		
		
	} catch(PDOException $ex){
	   $risposta = "Errore connessione: ".$ex->getMessage();
	} 		

?>






