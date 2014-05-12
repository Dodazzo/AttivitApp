<?php
/*
Questa pagina ricerca all'interno del database l'utente colleggato alla tessera tappata e ne ricava il saldo relativo all'attivita con id attivita = :id_attivita.
*/
//config.inc.php permette la connessione al DB.
require("config.inc.php");
if (!empty($_POST)) {
	    //Query ricerca utente
	    $query = "SELECT first_name, last_name, id FROM users WHERE hash_card = :hash_pass"; 
	    $query_params = array(':hash_pass' => $_POST['hash_pass']);
	    
	    try {
	        $stmt   = $db->prepare($query);
	        $result = $stmt->execute($query_params);
	    }
	    catch (PDOException $ex) {
	        $response["success"] = 0;
	        $response["message"] = "Database Error1. Riprova!";
	        die(json_encode($response));
	    }
	    $row = $stmt->fetch();	
	    if ($row) {
	            $login_ok = true;
	            $pass = "card";
				$firstname = $row["first_name"];
				$lastname = $row["last_name"];
				$id = $row["id"];
	    }
	    else {
		    $query = "SELECT first_name, last_name, id FROM users WHERE hash_keychain = :hash_pass"; 
	    	$query_params = array(':hash_pass' => $_POST['hash_pass']);
	    	try {
		        $stmt   = $db->prepare($query);
		        $result = $stmt->execute($query_params);
	    	}
		    catch (PDOException $ex) {
		        $response["success"] = 0;
		        $response["message"] = "Database Error2. Riprova!";
		        die(json_encode($response));
		    }
		    $row = $stmt->fetch();	
		    if ($row) {
		        $login_ok = true;
		        $pass = "keychain";
				$firstname = $row["first_name"];
				$lastname = $row["last_name"];
				$id = $row["id"];
		    }
		    else {
		    	 $response["success"] = 0;
       			 $response["message"] = "Utente non trovato :-(";
		    }	
	    }
    }
	//Query Saldo Utente
	$query = "SELECT coins FROM users_shops WHERE user_id = :id AND shop_id = :shop_id"; 
	$query_params = array(
	   ':id' => $row['id'],
	   ':shop_id' => $_POST['shop_id'],
    );
	//$id_attivita=$_POST['shop_id'];
	 try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Database Error3. Riprova! Post: {$_POST['shop_id']}";
        die(json_encode($response));
    }
	$row_2 = $stmt->fetch();
	if ($row_2) {
            $id_ok = true;
			$coins = $row_2["coins"];
	}
	else {
			$id_ok = true;
			$coins='0';
	}	
//Utente Trovato
    if ($login_ok && $id_ok) {
		$response["success"] = 1;
        $response["message"] = "Utente trovato :)";
		$response["first_name"] = $firstname;
		$response["last_name"] = $lastname;
		$response["id"] = $id;
		$response["pass"] = $pass;
		$response["coins"] = $coins;
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
        $response["message"] = "Utente non trovato --:(  Login Ok : {$login_ok} | Id Ok : {$id_ok} |";
        die(json_encode($response));
        
    }?>
		

