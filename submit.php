<?php

session_start();

/* upload pictures to S3 bucket */
require './vendor/autoload.php';   
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/* Connect to database */
$dbh = new PDO('mysql:host=localhost;dbname=akfoodie', 'root', 'BTS613forever-');
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* inputs from the submission form */
$b_name = $_POST['business'];
$b_description = $_POST['description'];
$la = $_POST['latitude'];
$lo = $_POST['longitude'];
$pic = "";
for($i=0;$i < count($_POST["fileToUpload"]); $i++){
    if($i == count($_POST["fileToUpload"])-1){
        $pic = $pic . $_POST["fileToUpload"][$i];
    }
    else{
        $pic = $pic . $_POST["fileToUpload"][$i] . ",";
    }
}

/* 
 * Data validation 
 *
 * At same location (same latitude and longitude), two business can not have the same name.
 * 
 * */
$check= 'SELECT * FROM objects WHERE ((business_name = ?) AND  (latitude = ?) AND (longitude = ?))'; 

$statement = $dbh->prepare($check);

try {
    $statement->execute([$b_name,$la,$lo]);
}
catch (PDOException $e) {
    echo "error!";
    echo $e->getMessage();
}
//number of duplication
$dup = $statement->FetchAll();
//if the business exists, return error and redirect to submission page in 3 second
if(count($dup) > 0){
    echo "error, business already existed! Please try another business name! The page will be redirected to the submission page in 3 second.";
    header( "refresh:3;url=submission.php" );
}
else{ //new business, then insert the object information into table `objects` and upload picture to S3 bucket.
    // submit new bussiness into the database
    $sql = "INSERT INTO objects (business_name,description,latitude,longitude,image_url,owner_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmnt = $dbh->prepare($sql);
    try {
        $stmnt->execute([$b_name,$b_description,$la,$lo,$pic,$_SESSION['ID']]);
        echo "successful!";
    }
    catch (PDOException $e) {
        echo "error!";
        echo $e->getMessage();
    }
    // AWS S3 and IAM Info
    $bucketName = 'akobject';
    $IAM_KEY = 'AKIAQ6LFSM5QBVG7C5L4';
    $IAM_SECRET = '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';
    // Connect to AWS S3 bucket
    try {
        $s3 = S3Client::factory(
            array(
                'credentials' => array(
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET
                ),
                'signature' => 'v4',
                'version' => 'latest',
                'region'  => 'ca-central-1'
            )
        );
    } catch (Exception $e) {
        // We use a die, so if this fails. It stops here. Typically this is a REST call so this would
        // return a json object.
        die("Error: " . $e->getMessage());
    }

    //number of picture need to upload
    $numfile = count($_FILES['fileToUpload']['name']);

    //get the object id that will be associated with the pictures
    $res = ($dbh->query('SELECT max(object_id) FROM objects'));
    $temp = $res->FetchAll();
    $obj_id = $temp[0]["max(object_id)"];

    //upload pictures to S3 bucket and insert picture path and associated object_id into files table
    $insertsql = 'INSERT INTO files (object_id, path) VALUES (?, ?)';
    $stmnt = $dbh->prepare($insertsql);
    for($i=0;$i < $numfile; $i++){ //for each picture, upload and insert into table one by one using for loop
        $keyName = 'akPic/' . basename($_FILES["fileToUpload"]['name'][$i]);
        $pathInS3 = 'https://'. $bucketName . '.s3.ca-central-1.amazonaws.com/' . $keyName;
        // Add it to S3   
        try {
            // Uploaded:
            $file = $_FILES["fileToUpload"]['tmp_name'][$i];
            $s3->putObject(
                array(
                    'Bucket'=>$bucketName,
                    'Key' =>  $keyName,
                    'SourceFile' => $file,
                    'StorageClass' => 'REDUCED_REDUNDANCY'
                )
            );
            try {
                $stmnt->execute([$obj_id,$pathInS3]);
                echo "successful!";
            }
            catch (PDOException $e) {
                echo "error!";
                echo $e->getMessage();
            }
            echo 'Done';
        } 
        catch (S3Exception $e) {
            die('Error:' . $e->getMessage());
        } 
        catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
        
    }

    // redirect to created individual object page
    header("Location: https://{$_SERVER['HTTP_HOST']}/individual_detail.php?object_id=$obj_id");
}



?>