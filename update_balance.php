<?php
/*
Questa pagina riceve in post i dati da UpdateCoins.Java ed è adibita all'aggiornamento del saldo dell'utente :id_utente all'interno dell'attivita :id_attivita. 
Qualora fosse la prima volta che l'utente visita il locale, la pagina provvederà a creare il record.
*/
//config.inc.php permette la connessione al DB.
require("config.inc.php");

date_default_timezone_set('Europe/Rome'); 
$date = date('Y-m-d H:i:s');
//Se il campo post['GIFT'] è vuoto, eseguo l'if
if (empty($_POST['gift'])){
$check = "INSERT INTO receipts (user_id, shop_id, total, created_at) VALUES (:id_utente, :id_attivita, :saldo, :date)";
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
}
//Se il campo post['GIFT'] non è vuoto, eseguo l'else
else {
$check = "INSERT INTO prizes (shop_id, user_id, shop_product_id, created_at) VALUES (:id_attivita, :id_utente, :gift_id)";
$query_params0 = array(
	   ':id_utente' => $_POST['id_utente'],
	   ':id_attivita' => $_POST['id_attivita'],
	   ':gift_id' => $_POST['gift_id'],
	   //':date' => $date,
    );
 try {
        $stmt   = $db->prepare($check);
        $result = $stmt->execute($query_params0);
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Database Error = 2.!";
        die(json_encode($response));
    }
$row = $stmt->fetch();
$response["success"] = 1;
$response["message"] = "Aggiunto con successo il check in YO! {$date}";
}

//Controllo se c'è già un record nel DB users_shops relativo all'utente
$query = "SELECT coins FROM users_shops WHERE user_id = :id_utente";    
$query_params = array(':id_utente' => $_POST['id_utente']);
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
if ($row) {
	$coin_trovato = $row['coins'];
	// Se è un prodotto premio:
	if ($_POST['gift']){
	$query = "UPDATE users_shops SET coins = coins - :saldo WHERE user_id = :id_utente AND shop_id = :id_attivita";
	$query_params = array(
	   		':id_utente' => $_POST['id_utente'],
	   		':id_attivita' => $_POST['id_attivita'],
			':saldo' => $_POST['saldo'],
	    );
	try {
      	  $stmt   = $db->prepare($query);
       	  $result = $stmt->execute($query_params);
   		 }
   		 catch (PDOException $ex) {
     	  $response["success"] = 0;
      	  $response["message"] = "Database Error = 4. ";
         die(json_encode($response));
   		 }
	$row = $stmt->fetch();
	$saldo_aggiornato=$coin_trovato-($_POST['saldo']);
	$response["success"] = 1;
	$response["message"] = "Regalo aggiunto! Update coins avvenuto con successo!";
	$response["coins_inseriti"]= $_POST['saldo'];
	$response["saldo"]= $saldo_aggiornato;
	die(json_encode($response));
	}
	//Se non è un prodotto premio:
	else {
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
      	  $response["message"] = "Database Error = 5. ";
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
}

else {
	if (empty($_POST['gift'])){
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
 	  $response["message"] = "Database Error = 6. Riprova!";
 	  die(json_encode($response));
	}
	$saldo_aggiornato=($_POST['saldo']*10);
	$response["success"] = 1;
	$response["message"] = "Record Utente inserito con successo (aggiornato)!";
	$response["saldo"] = $saldo_aggiornato;
	die(json_encode($response));
	}
	else {
	$response["message"] = "Database Error = 7. Riprova!";
 	die(json_encode($response));
	}
}




?>
