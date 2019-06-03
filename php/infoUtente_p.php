<?php
	
	
	$user = "root"; 
	$pwd = ""; 
	$risposta = "";
	
	$dbname = "bancaore";
	$hostname = "localhost";
	
	try{
		
		$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
		$codUt = $_POST['codice'];
		
		$stmt = $conn->prepare("SELECT nome, cognome, dataNascita, telefono, email, indirizzo, paese, cap, bio, categoria, foto
								  FROM utenti JOIN categorie ON utenti.codiceCategoria = categorie.codCategoria
								 WHERE codiceUtente = :cod");
				 
		$stmt->bindParam(':cod', $codUt, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$result = $stmt->fetch();
		
		$nome = $result['nome'] . " " . $result['cognome'];
		$foto = "/bancaore/img/utenti/" . $result['foto'];
		$categoria = ucfirst(strtolower($result['categoria']));
		
		$dati = array("nome"=>$nome, "dataNascita"=>$result['dataNascita'], "telefono"=>$result['telefono'], "email"=>$result['email'], "indirizzo"=>$result['indirizzo'], "paese"=>$result['paese'], "cap"=>$result['cap'], "bio"=>$result['bio'], "categoria"=>$categoria, "foto"=>$foto);
		
		echo json_encode($dati);
		
		$conn = null;
	} catch(PDOException $ex){
	   echo "Errore connessione: ".$ex->getMessage();
	} 
	
	
?>