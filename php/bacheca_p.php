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
		
		/*** CALCOLO ORE CREDITO ***/
				
		$stmt = $conn->prepare("SELECT codEsecutore, sum(importo) as OreCredito
								  FROM movimenti
								 WHERE codEsecutore = :codEs
							  GROUP BY codEsecutore");
				 
		$stmt->bindParam(':codEs', $codiceUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		$oreCredito = $result['OreCredito'];
		
		if ($oreCredito === null) {
			$oreCredito = 0;
		}
		
		/*** CALCOLO ORE DEBITO ***/
		
		$stmt = $conn->prepare("SELECT codCommittente, sum(importo) as OreDedito
								  FROM movimenti
								 WHERE codCommittente = :codCom
							  GROUP BY codCommittente");
				 
		$stmt->bindParam(':codCom', $codiceUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		$oreDebito = $result['OreDedito'];
		
		if ($oreDebito === null) {
			$oreDebito = 0;
		}
		
		/*** CALCOLO ORE SALDO ***/
		
		$saldo = strval($oreCredito - $oreDebito);
		
		/*** CALCOLO LAVORI ***/
		
		$stmt = $conn->prepare("SELECT count(codAnnuncio) AS lavoriInCorso 
								  FROM annunci 
								 WHERE committente = :codCom AND LCT = 'L' 
							  GROUP BY committente");
							  
		$stmt->bindParam(':codCom', $codiceUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		$lavori = $result['lavoriInCorso'];
		
		if ($lavori === null) {
			$lavori = 0;
		}
		/*** DATI + DETTAGLI ***/
		
		$stmt = $conn->prepare("SELECT nomeUtente, nome, cognome, dataNascita, telefono, email, indirizzo, paese, cap, bio, categoria, foto
								  FROM utenti JOIN categorie ON utenti.codiceCategoria = categorie.codCategoria
								 WHERE codiceUtente = :cod");
				 
		$stmt->bindParam(':cod', $codiceUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		$username = $result['nomeUtente'];
		$nome = $result['nome'] . " " . $result['cognome'];
		$dataNascita = date("d-m-Y", strtotime($result['dataNascita']));
		$telefono = $result['telefono'];
		$email = $result['email'];
		$indirizzo = $result['indirizzo'];
		$paese = $result['paese'];
		$cap = $result['cap'];
		$bio = $result['bio'];
		$categoria = ucfirst(strtolower($result['categoria']));
		$foto = "/bancaore/img/utenti/" . $result['foto'];
		
		// foto
		
		$dati = array($oreCredito, $oreDebito, $saldo, $username, $nome, $dataNascita, $telefono, $email, $indirizzo, $paese, $cap, $bio, $categoria, $lavori, $foto);
		echo json_encode($dati);
		
	} catch(PDOException $ex){
	   $risposta = "Errore connessione: ".$ex->getMessage();
	} 
	
	
	
?>