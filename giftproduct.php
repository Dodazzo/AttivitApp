<?php

/*
Our "config.inc.php" file connects to database every time we include or require
it within a php script.  Since we want this script to add a new user to our db,
we will be talking with our database, and therefore,
let's require the connection to happen:
*/
require("config.inc.php");
//Selezioni i prodotti premio che l'utente può permettersi
$check = "SELECT * FROM shops_products sp NATURAL JOIN products p WHERE (p.shop_id = :id_attivita AND p.type = 'price') AND (p.coins <= :coins_user)";
//Inizializzo parametri
$query_params = array(
	   ':id_attivita' => $_POST['id_attivita'],
	   ':users_coins' => $_POST['users_coins'],
    );
 try {
        $stmt   = $db->prepare($check);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Database Error = 1. Riprova!";
        die(json_encode($response));
    }
	
$row = $stmt->fetchAll();
if ($row) {
    $response["success"] = 1;
    $response["message"] = "Prodotti premio disponibili!";
    $response["products"]   = array();
    
    foreach ($row as $row) {
        $post             = array();
		$post["name"]  = $row["p.name"];
        $post["avatar"] = $row["p.avatar"];
		$post["coins"] = $row["ps.coins"];
        //update our repsonse JSON data
           //update our repsonse JSON data
        array_push($response["products"], $post);
    }
        // echoing JSON response
    echo json_encode($response);
        
} else {
    $response["success"] = 0;
    $response["message"] = "Non ci sono prodotti disponibili!";
    die(json_encode($response));
}

?>


