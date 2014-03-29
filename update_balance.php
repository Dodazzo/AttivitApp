<?php
/*
Questa pagina riceve in post i dati da UpdateCoins.Java ed è adibita all'aggiornamento del saldo dell'utente :id_utente all'interno dell'attivita :id_attivita. 
Qualora fosse la prima volta che l'utente visita il locale, la pagina provvederà a creare il record.
*/
//config.inc.php permette la connessione al DB.
require("config.inc.php");

//Query di controllo 
date_default_timezone_set('Europe/Rome'); 
$date = date('Y-m-d H:i:s');
$check = "INSERT INTO receipts (user_id, shop_id, total, created_at) VALUES (:id_utente, :id_attivita, :saldo, :date);";
//Inizializzo parametri
$query_params0 = array(
	   ':id_utente' => $_POST['id_utente'],
	   ':id_attivita' => $_POST['id_attivita'],
	   ':saldo' => (str_replace(",",".", $_POST['saldo'])*10),
	   ':date' => $date,
    );
 try {
        $stmt   = $db->prepare($check);
        $result = $stmt->execute($query_params0);
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Database Error = 1.!";
        die(json_encode($response));
    }
$row = $stmt->fetch();
$response["success"] = 1;
$response["message"] = "Aggiunto con successo il check in YO! {$date}";

//Controllo se c'è già un record nel DB users_shops relativo all'utente
$query = "SELECT coins FROM users_shops WHERE user_id = :id_utente";    
$query_params = array(':id_utente' => $_POST['id_utente']);
try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Database Error = 2. ";
        die(json_encode($response));
    }
$row = $stmt->fetch();
if ($row) {
	$coin_trovato = $row['coins'];
	//Record utente_shops trovato!
	//if (!empty($_POST['gift']))
	// Se è un regalo:
	//	$query = "UPDATE user_shops SET coins = coins - :saldo WHERE user_id = :id_utente AND shop_id = :id_attivita";
	//else 
	// Se no:
		$query = "UPDATE users_shops SET coins = coins + :saldo WHERE user_id = :id_utente AND shop_id = :id_attivita";
		$query_params = array(
	   		':id_utente' => $_POST['id_utente'],
	   		':id_attivita' => $_POST['id_attivita'],
			':saldo' => (str_replace(",",".", $_POST['saldo'])*10),
	    );
		try {
      	  $stmt   = $db->prepare($query);
       	  $result = $stmt->execute($query_params);
   		 }
   		 catch (PDOException $ex) {
     	  $response["success"] = 0;
      	  $response["message"] = "Database Error = 3. ";
         die(json_encode($response));
   		 }
		$row = $stmt->fetch();
		$saldo_aggiornato=$coin_trovato+($_POST['saldo']*10);
		$response["success"] = 1;
		$response["message"] = "Update coins avvenuto con successo!";
		$response["coins_inseriti"]= $_POST['saldo']*10;
		$response["saldo"]= $saldo_aggiornato;
		die(json_encode($response));
}

else {
	//Se non c'è un record nel DB dell'utente nella tabella mie_attivita, creo il record
	$query = "INSERT INTO users_shops (user_id, shop_id, coins) VALUES (:id_utente, :id_attivita, :saldo);";
	$query_params = array(
      ':saldo' => (str_replace(",",".", $_POST['saldo'])*10),
	  ':id_utente' => $_POST['id_utente'],
	  ':id_attivita' => $_POST['id_attivita'],
      );
//Eseguo la query
	try {
  	  $stmt   = $db->prepare($query);
  	  $result = $stmt->execute($query_params);
	}
	catch (PDOException $ex) {
	  $response["success"] = 0;
 	  $response["message"] = "Database Error = 4. Riprova!";
 	  die(json_encode($response));
	}
	$saldo_aggiornato=($_POST['saldo']*10);
	$response["success"] = 1;
	$response["message"] = "Record Utente inserito con successo (aggiornato)!";
	$response["saldo_aggiornato"] = $saldo_aggiornato;
	die(json_encode($response));
}



	
  // 	 	$query = "UPDATE mie_attivita SET saldo_attivita = saldo_attivita + :saldo WHERE id_utente = :id_utente AND id_attivita = :id_attivita";
//	$query_params = array(
 //     ':saldo' => $_POST['saldo'],
//	  ':id_utente' => $_POST['id_utente'],
//	  ':id_attivita' => $_POST['id_attivita'],
 //     );
//Eseguo la query
//	try {
//  	  $stmt   = $db->prepare($query);
//  	  $result = $stmt->execute($query_params);
//	}
//	catch (PDOException $ex) {
//	  $response["success"] = 0;
// 	  $response["message"] = "Database Error = 2. Riprova!";
// 	  die(json_encode($response));
//	}
// Aggiornamento Saldo
//    $response["success"] = 1;
//    $response["message"] = "Saldo Aggiornato!";
//Seleziono il saldo aggiornato
//	$query_saldo = "SELECT saldo_attivita FROM mie_attivita WHERE id_utente = :id_utente AND id_attivita = :id_attivita";
//	$query_params1 = array(
//	  ':id_utente' => $_POST['id_utente'],
//	  ':id_attivita' => $_POST['id_attivita'],
 //     );
//	 try {
 // 		  $stmt2   = $db->prepare($query_saldo);
  //	 	  $result = $stmt2->execute($query_params1);
//		}
	//    catch (PDOException $ex) {
	//	  $response["success"] = 0;
 //		  $response["message"] = "Database Error = 2bis. Riprova!";
 //	    die(json_encode($response));
//	    }
//		$row2 = $stmt2->fetch();
//Se trovo il saldo aggiungo il JSON relativo...
//		if ($row2) {
//			$saldo_utente=$row2;
//			$response["saldo_aggiornato"] = $saldo_utente;
//			die(json_encode($response));
//		}
     // echoing JSON response
//}

// Aggiornamento Saldo
  //  $response["success"] = 1;
  //  $response["message"] = "Record Creato e saldo Aggiornato!";
     // echoing JSON response
 //   echo json_encode($response);
//}
?>
