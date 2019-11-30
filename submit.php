<?php
// submission
session_start();

// connect to the database
$dbh = new PDO('mysql:host=localhost;dbname=akfoodie', 'root', 'BTS613forever-');
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* inputs from the form */
// POST Variable
$b_name = $_POST['business'];
$b_description = $_POST['description'];
$la = $_POST['latitude'];
$lo = $_POST['longitude'];

$test = "";
for($i=0;$i < count($_POST["file"]); $i++){
    if($i == count($_POST["file"])-1){
        $test = $test . $_POST["file"][$i];
    }
    else{
        $test = $test . $_POST["file"][$i] . ",";
    }
}

/*
 * $check= "SELECT * FROM objects WHERE ((business = ?) AND  (latitude = ?) AND (longitude = ?))" 


$test = $dbh->query($check);

echo $test->rowCount();

// if the account exists, return error
if($test->rowCount() > 0){
    echo "error";
}
else{
    加下面那段 insert
}
 * 
 */
// submit new bussiness into the database

$sql = "INSERT INTO objects (business_name,description,latitude,longitude,image_url,owner_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmnt = $dbh->prepare($sql);

try {
    $stmnt->execute([$b_name,$b_description,$la,$lo,$test,$_SESSION['ID']]);
    echo "successful!";
}
catch (PDOException $e) {
    echo "error!";
    echo $e->getMessage();
}
//header("Location: https://{$_SERVER['HTTP_HOST']}/registration.html");

/* upload pictures to S3 bucket */
require './vendor/autoload.php';
	
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
// AWS Info
$bucketName = 'akobject';
$IAM_KEY = 'AKIAQ6LFSM5QPYKZKVZR';
$IAM_SECRET = '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';
// Connect to AWS
try {
    // You may need to change the region. It will say in the URL when the bucket is open
    // and on creation.
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

$numfile = count($_FILES['fileToUpload']['name']);
$res = ($dbh->query('SELECT max(object_id) FROM objects'));
$temp = $res->FetchAll();
$obj_id = $temp[0]["max(object_id)"];
echo $obj_id;
$insertsql = 'INSERT INTO files (object_id, path) VALUES (?, ?)';
$stmnt = $dbh->prepare($insertsql);
for($i=0;$i < $numfile; $i++){
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

// redirect to individual page
header("Location: https://{$_SERVER['HTTP_HOST']}/individual_detail.php?object_id=$obj_id");


?>