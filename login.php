<?php
    
//load and connect to MySQL database stuff
require("config.inc.php");
require("password.php"); 

if (!empty($_POST)) {
    //gets user's info based off of a username.
    $query = "SELECT name, id, email, encrypted_password FROM shops WHERE email = :username";    
    $query_params = array(':username' => $_POST['username']);
    
    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        //die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error1. Riprova!";
        die(json_encode($response));
        
    }
    
    //This will be the variable to determine whether or not the user's information is correct.
    //we initialize it as false.
    $validated_info = false;
    //fetching all the rows from the query
    $row = $stmt->fetch();
    if ($row) {
        //if we encrypted the password, we would unencrypt it here, but in our case we just
        //compare the two passwords
		
		$hash = $row['encrypted_password'];

		if (password_verify($_POST['password'], $hash)) {
   		    //$response["message"] = "You have successfully logged in!";
            $login_ok = true;
            //echo $response["message"];
		} 
		else {
            //$response["message"] = "Invalid username or password";
            $login_ok = false;
            //echo $response["message"];
		}
	}
    
    // If the user logged in successfully, then we send them to the private members-only page 
    // Otherwise, we display a login failed message and show the login form again 
    if ($login_ok) {
		$nome_attivita=$row['name'];
        $response["success"] = 1;
        $response["message"] = "Buongiorno {$nome_attivita}!";
		$response["id_attivita"] = $row['id'];
        die(json_encode($response));
    } else {
        $response["success"] = 0;
        $response["message"] = "Credenziali sbagliate!";
        die(json_encode($response));
    }
} else {
?>
		<h1>Login</h1> 
		<form action="login.php" method="post"> 
		    Username:<br /> 
		    <input type="text" name="username" placeholder="username" /> 
		    <br /><br /> 
		    Password:<br /> 
		    <input type="password" name="password" placeholder="password" value="" /> 
		    <br /><br /> 
		    <input type="submit" value="Login" /> 
		</form> 
		<a href="register.php">Register</a>
	<?php
}

?> 
