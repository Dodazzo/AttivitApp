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
    $firstname = $stmt->fetch();
	$response["message"] = "{$firstname[0]}";
	die(json_encode($response));
    if ($row) {
            $login_ok = true;

			//$id_utente=$row['id'];
        }
    }
//Utente Trovato
    if ($login_ok) {
		$response["success"] = 1;
        $response["message"] = "Utente Unipiazza letto con successo!";

		$response["first_name"] = $firstname;
		die(json_encode($response));

		//$response["id"] = $id_utente;
		//die(json_encode($response));
//Recupero record riguardo al saldo attivita
		
//Recupero l'id utente dall'hash della tessera
		//$query_id_utente = "SELECT id FROM users WHERE hash_tessera = :hash_tessera";
		//$query_params2 = array(
		//	':hash_tessera' => $_POST['hash_tessera'],
		//);
		//try {
  	//	  $stmt3   = $db->prepare($query_id_utente);
  	 	//  $result2 = $stmt3->execute($query_params2);
		//}
	  //  catch (PDOException $ex) {
		//  $response["success"] = 0;
 		//  $response["message"] = "Database Error = 3. Riprova!";
 	   // die(json_encode($response));
	   // }
		//$row3 = $stmt3->fetch();

	}
	else {
        $response["success"] = 0;
        $response["message"] = "Utente non trovato =(";
        die(json_encode($response));
        
    }?>
		

