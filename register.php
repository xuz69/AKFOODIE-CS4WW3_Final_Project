<?php
//registration page

//connecting to the database
$dbh = new PDO('mysql:host=localhost;dbname=akfoodie', 'root', 'BTS613forever-');

$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// POST variables
$firstname = $_POST['first'];
$lastname = $_POST['last'];
$e_mail = $_POST['email'];
$pass = $_POST['pwd'];
$coun = $_POST['country'];
$code = $_POST['postal'];

/*
 * MySQL
 * check whether the account already exists
 * avoid duplicate name of the user and duplicate email address exist in the database
 */
$check = "SELECT * FROM users WHERE (email = '$e_mail')";

$test = $dbh->query($check);


// if the account exists, return error message.
if($test->rowCount() > 0){
    echo "error, the email address you have entered is already registered. Redirect to registration page in 5 sec.";
    header( "refresh:5;url=registration.html" );
}
else{
    // else insert the new account into the database
    $sql = "INSERT INTO users (first_name, last_name, email, password, country, postal_code) VALUES (?, ?, ?, ?, ?, ?)";
    $stmnt = $dbh->prepare($sql);
    
    try {
        $stmnt->execute([$firstname,$lastname,$e_mail,$pass,$coun,$code]);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    // redirect to login page
    header("Location: https://{$_SERVER['HTTP_HOST']}/login.html");
}


?>
