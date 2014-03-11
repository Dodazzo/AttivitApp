<?php

/*
Our "config.inc.php" file connects to database every time we include or require
it within a php script.  Since we want this script to add a new user to our db,
we will be talking with our database, and therefore,
let's require the connection to happen:
*/
require("config.inc.php");

//initial query
//$query = "SELECT * FROM prodotti WHERE id_attivita = :id_attivita";
// $query = "SELECT * FROM prodotti p NATURAL JOIN attivita a WHERE a.username = :username "; 
$query = "SELECT p.nome, p.categoria, pa.gettoni FROM (prodotti_attivita pa NATURAL JOIN prodotti p) JOIN attivita a ON (a.id_attivita = pa.id_attivita) WHERE a.username = :username AND tipo = 'normale'"; 

$query_params = array(':username' => 'torchio');
//$query_params = array(':id_attivita' => $_POST['id_attivita']);
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

// Finally, we can retrieve all of the found rows into an array using fetchAll 
$rows = $stmt->fetchAll();


if ($rows) {
    $response["success"] = 1;
    $response["message"] = "Prodotti disponibili!";
    $response["products"]   = array();
    
    foreach ($rows as $row) {
        $post             = array();
		$post["nome"]  = $row["nome"];
		$post["categoria"]  = $row["categoria"];
        $post["immagine"] = $row["immagine"];
		$post["gettoni"] = $row["gettoni"];
        
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
