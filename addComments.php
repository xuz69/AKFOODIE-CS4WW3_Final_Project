<?php
// add comments for restaurant
// connecting to the database
$dbh = new PDO('mysql:host=localhost;dbname=akfoodie', 'root', 'BTS613forever-');

$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

session_start();
// Check if session is a logged in one, if it isn't then redirect to login.
if (!isset($_SESSION['ID'])){
    header("Location: https://{$_SERVER['HTTP_HOST']}/login.html");
}
else{
    // after account login

    // Session variables
    $user = $_SESSION['ID'];

    // POST variables, comment content and rating
    $comment = $_POST["comment"];
    $rate = $_POST["rating"];

    // GET variables, object_id
    $id = $_GET["object_id"];

    /*
     * MySQL
     * insert users' reviews(rating for the store, visit date, and comments) into database
     */
    $sql = 'INSERT INTO reviews (rating, date, content, user_id, object_id) VALUES (?,CURDATE(),?,?,?)';
    
    //Prepares a statement for execution and returns a statement object
    $stms = $dbh->prepare($sql);
    try {
        $stms->execute([$rate,$comment,$user,$id]);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }

    /* manage the rate of the object */
    $sql = 'SELECT * FROM reviews WHERE object_id = :id ';
    $sql1 = 'SELECT sum(rating) as sum1 FROM reviews WHERE object_id = :id';

    $stms = $dbh->prepare($sql);
    $stms1 = $dbh->prepare($sql1);
    try {
        $stms->execute(array("id"=>$id));
        $stms1->execute(array("id"=>$id));
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    // Returns an array containing all of the result set rows
    $reviews = $stms->fetchAll();
    $sum = $stms1->fetchAll();

    // count reviews and update the average rate for restaurant
    $count = count($reviews);
    $new_rate = ($sum[0]["sum1"])/($count);
    

    /* update new rate into object*/
    $sql2 = 'UPDATE objects SET rating = :new WHERE object_id = :id ';
    $stms2 = $dbh->prepare($sql2);
    
    try {
        $stms2->execute(array("new"=>$new_rate,"id"=>$id));
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
    
    // redirect the web location
    header("Location: https://{$_SERVER['HTTP_HOST']}/individual_detail.php?object_id=$id");
}

?>