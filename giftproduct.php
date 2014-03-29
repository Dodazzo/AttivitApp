<?php

/*
Our "config.inc.php" file connects to database every time we include or require
it within a php script.  Since we want this script to add a new user to our db,
we will be talking with our database, and therefore,
let's require the connection to happen:
*/
require("config.inc.php");
//Selezioni i prodotti premio che l'utente può permettersi
$check = "SELECT p.name, sp.coins FROM shops_products sp JOIN products p ON (sp.product_id = p.id) WHERE sp.shop_id = :id_attivita";
// AND product_type = 'prize')
//Inizializzo parametri
$query_params = array(':id_attivita' => $_POST['id_attivita']);
 try {
        $stmt   = $db->prepare($check);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Database Error = 1. Riprova!";
        die(json_encode($response));
    }
	
$rows = $stmt->fetchAll();
if ($rows) {
    $response["success"] = 1;
    $response["message"] = "Prodotti premio disponibili!";
    $response["products"]   = array();
    
    foreach ($rows as $row) {
        $products             = array();
		$products["name"]  = $row["name"];
		$products["coins"] = $row["coins"];
        //update our repsonse JSON data
        array_push($response["products"], $products);
    }
        // echoing JSON response
    echo json_encode($response);
        
} else {
    $response["success"] = 0;
    $response["message"] = "Non ci sono prodotti disponibili!";
    die(json_encode($response));
}

?>


