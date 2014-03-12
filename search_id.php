<?php
/*
Questa pagina ricerca all'interno del database l'utente colleggato alla tessera tappata e ne ricava il saldo relativo all'attivita con id attivita = :id_attivita.
*/
//config.inc.php permette la connessione al DB.
require("config.inc.php");
if (!empty($_POST)) {
    //Query ricerca utente
    $query = "SELECT first_name FROM users WHERE hash_tessera = :hash_tessera"; 
    $query_params = array(':hash_tessera' => $_POST['hash_tessera']);
    
    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Database Error1. Riprova!";
        die(json_encode($response));
    }
	$validated_info = false;
    $row = $stmt->fetch();
    if ($row) {
            $login_ok = true;
			$user=$row;
        }
    }
//Utente Trovato
    if ($login_ok) {
		$response["success"] = 1;
        $response["message"] = "Utente Unipiazza letto con successo!";
		$response["user"] = $user;
//Recupero record riguardo al saldo attivita
		$query_saldo = "SELECT saldo_attivita FROM mie_attivita NATURAL JOIN utenti WHERE hash_tessera = :hash_tessera AND id_attivita = :id_attivita";
		$query_params1 = array(
			':hash_tessera' => $_POST['hash_tessera'],
			':id_attivita' => $_POST['id_attivita'],
		);
		try {
  		  $stmt2   = $db->prepare($query_saldo);
  	 	  $result = $stmt2->execute($query_params1);
		}
	    catch (PDOException $ex) {
		  $response["success"] = 0;
 		  $response["message"] = "Database Error = 2. Riprova!";
 	    die(json_encode($response));
	    }
		$row2 = $stmt2->fetch();
//Recupero l'id utente dall'hash della tessera
		$query_id_utente = "SELECT id FROM users WHERE hash_tessera = :hash_tessera";
		$query_params2 = array(
			':hash_tessera' => $_POST['hash_tessera'],
		);
		try {
  		  $stmt3   = $db->prepare($query_id_utente);
  	 	  $result2 = $stmt3->execute($query_params2);
		}
	    catch (PDOException $ex) {
		  $response["success"] = 0;
 		  $response["message"] = "Database Error = 3. Riprova!";
 	    die(json_encode($response));
	    }
		$row3 = $stmt3->fetch();
//Se trovo il saldo aggiungo il JSON relativo...
		if ($row2) {
			$saldo_attivita=$row2;
			$id_utente=$row3;
			$response["saldo_utente"] = $saldo_attivita;
			$response["id_utente"] = $id_utente;
			die(json_encode($response));
		}
//Se no rimando indietro solamente l'$user
		else 
			die(json_encode($response));
	}
	else {
        $response["success"] = 0;
        $response["message"] = "Utente non trovato =(";
        die(json_encode($response));
        
    }?>
		

