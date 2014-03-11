<?php

/*
Our "config.inc.php" file connects to database every time we include or require
it within a php script.  Since we want this script to add a new user to our db,
we will be talking with our database, and therefore,
let's require the connection to happen:
*/
require("config.inc.php");

//initial query
$query = "SELECT saldo_attivita FROM mie_attivita WHERE id_utente = :id_utente AND id_attivita = :id_attivita";
//UPDATE mie_attivita SET saldo_attivita = saldo_attivita + '50' WHERE id_utente = 1
 //Update query
   $query_params = array(
	   ':id_utente' => $_POST['id_utente'],
	   ':id_attivita' => $_POST['id_attivita'],
    );
//execute query
try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
}
catch (PDOException $ex) {
    $response["success"] = 0;
    $response["message"] = "Database Error!";
    die(json_encode($response));
}
$rows = $stmt->fetchAll();

// Finally, we can retrieve all of the found rows into an array using fetchAll 

    $response["success"] = 1;
    $response["message"] = "Saldo Aggiornato!";
	$response["saldo_attivita"] = $row['saldo_attivita'];
     // echoing JSON response
    echo json_encode($response);

?>
