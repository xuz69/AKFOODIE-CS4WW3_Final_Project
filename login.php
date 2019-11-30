<?php
// Login Page
session_start();

// connecting to database
$dbh = new PDO('mysql:host=localhost;dbname=akfoodie', 'root', 'BTS613forever-');

$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// POST variables
$in_email = $_POST['email'];
$in_password = $_POST['password'];

/* 
 * MySQL - login
 * check whether the user exist on the databae
 */
$sql = "SELECT * FROM users WHERE ((email = ?) AND (password = ?))";

//Prepares a statement for execution and returns a statement object
$stmnt = $dbh->prepare($sql);
try {
    $stmnt->execute([$in_email,$in_password]);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Returns an array containing all of the result set rows
$rows = $stmnt->fetchAll();

// if the account exists, login successed
if (count($rows) == 1){
    // Setting the session to the returned user ID.
    $_SESSION['ID'] = $rows[0]['user_id'];
    echo "successed";
    // Redirect to table of users.
    header("Location: https://{$_SERVER['HTTP_HOST']}/search.html");
} else {
    echo "error";
}

?>