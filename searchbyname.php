<?php 
// Connect to the server       
$dbh = new PDO('mysql:host=localhost;dbname=akfoodie', 'root', 'BTS613forever-');

$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// POST Variables
$name = $_POST['name'];
$order = $_POST['rating'];

// rating select from lowest to heighest or from heighest to lowest
if($order == "LowestToHighest"){
    $sql = 'SELECT * FROM objects WHERE business_name LIKE :store ORDER BY rating ASC';
}else{
    $sql = 'SELECT * FROM objects WHERE business_name LIKE :store ORDER BY rating DESC';
}
//Prepares a statement for execution and returns a statement object
$stms = $dbh->prepare($sql);
try {
    $stms->execute(array("store"=>'%'.$name.'%'));
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Returns an array containing all of the result set rows
$results = $stms->fetchAll();
$count = count($results);

// select the restaurant by its name
$sql_pic = 'SELECT * from files WHERE object_id = :id';
$id = $results[$i]["object_id"];
$stms1 = $dbh->prepare($sql_pic);
try {
    $stms1->execute(array("id"=>$id));
} catch (PDOException $e) {
    echo $e->getMessage();
}
$targets = $stms1->fetchAll();

// rating star for the restaurant
function star($input){
    if($input < 1){
        echo '<span class="fa fa-star"></span> 
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>';
    }
    else if(1<=$input && $input <2){
        echo '<span class="fa fa-star checked"></span> 
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>';
    }
    else if(2<=$input && $input <3){
        echo '<span class="fa fa-star checked"></span> 
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>';
    }
    else if(3<=$input && $input <4){
        echo '<span class="fa fa-star checked"></span> 
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star"></span>
        <span class="fa fa-star"></span>';
    }
    else if(4<=$input && $input <5){
        echo '<span class="fa fa-star checked"></span> 
        <span class="fa fa-star checked" ></span>
        <span class="fa fa-star checked" ></span>
        <span class="fa fa-star checked" ></span>
        <span class="fa fa-star"></span>';
    }
    else{
        echo '<span class="fa fa-star checked"></span> 
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star checked"></span>
        <span class="fa fa-star checked" ></span>';
    }

}

/* upload pictures to S3 bucket */
require './vendor/autoload.php';
	
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
// AWS Info
$bucketName = 'object-pic';
$IAM_KEY = 'AKIAQ6LFSM5QFS7U3YF5';
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

?>
<!DOCTYPE html>
<html>
<!-- head element -->
<head>
    <title> Results </title>

    <!-- control layout of the page on mobile brower -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.5">
    <!-- link to the external css file resource, "results_style.css" is the same folder -->
    <link rel="stylesheet" type="text/css" href="results_style.css">
    <!-- this source is searched online because I need this to show the rating stars -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- link to map js -->
    <!--script src="sample_results_map.js" defer></script-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDl-wq_Axw2CMLmefmsz9VPC1sM0ITeHhI&callback=initMap" defer></script> <!-- Use the google map API and call the function in external js -->
</head>
<!-- body element -->
<body>
    <!-- header element -->
    <header>
        <div class="column logo"> 
        <!--the logo of the website is an image, clicking the logo will be linked to the search page (home page) -->
        <a href="search.html"><img src="ak.jpg" alt="" width = "50" height="50"></a>
        </div>

        <!--navigation menu-->
        <!-- There are five links to other pages, such as login page, sign up page, home page etc. -->
        <div class="column top-navigation">
            <a href="login.html"> Log In</a>
            <a href="registration.html"> Sign Up</a>
            <a href="logout.php"> Log Out</a>
            <a href="submission.php"> New Business </a>
            <a href="search.html"> Home </a>
        </div>
    </header>

    <?php if($count != 0){?>
    <!-- main content -->
    <main>
        <!-- information to instruct user that results are listed below-->
        <div class="result-label">
            <h1> Results: </h1>
            <p> All the results are listed below: <?php echo $count;?> result(s) </p>
        </div>

        <!--in the row class, we seperate the results table and map in two columns -->
        <!--div class="row"-->
            <!-- results table part -->
        
            <div class="column results"> 
                <!-- each table cell used to store a "single-object" -->
                <table> 
                    <?php for($i=0;$i<$count;$i++){ ?>
                        <?php
                        // Verify valid access code
                        $sql_pic = 'SELECT * from files WHERE object_id = :id';
                        $id = $results[$i]["object_id"];
                        $stms1 = $dbh->prepare($sql_pic);
                        try {
                            $stms1->execute(array("id"=>$id));
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }
                        $targets = $stms1->fetchAll();
                        ?>
                    <tr>
                        <th> 
                            <!-- single_object contains two columns, first column contains the food pic and the second column contains the other information about the restaurant -->
                            <div class="single_object">
                                <div class="food-pic">
                                        <!-- There is a link to page about the details of specific object after clicking the image -->
                                        <a href="individual_detail.php?object_id=<?php echo $results[$i]["object_id"] ?> ">
                                            <img src=<?php echo $targets[0]["path"];?> alt="opps">
                                        </a>
                                </div>
                                <!-- object information part -->
                                <div class="single_object_info">
                                    <div class="object-name"> <a href="individual_detail.php?object_id=<?php echo $results[$i]["object_id"] ?> "><?php echo $results[$i]["business_name"]?> </a></div> <!-- name of the restaurant -->
                                    <div class="object-rating"> 
                                        <?php star($results[$i]["rating"]);?>
                                        <span> <?php echo $results[$i]["rating"]; ?> </span> <!-- ratings -->
                                    </div>
                                    <div class="object-location">
                                        <span> (<?php echo $results[$i]["latitude"];?>,<?php echo $results[$i]["longitude"];?>) </span> <!-- location of the restaurant -->
                                    </div>
                                    <div class="object-description">
                                        <!-- description of the restaurant -->
                                        <p> <?php echo $results[$i]["description"]; ?> </p>
                                    </div>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <?php } ?>
                </table>
            </div>

            <!-- map column -->
            <div class="column map">
                <div id="map"></div>
            </div>

        <!--/div-->
    </main>
    <?php } else{ ?>
        <main> 
            <!-- information to instruct user that results are listed below-->
            <div class="result-label">
                <h1> Results: </h1>
                <p> All the results are listed below: <?php echo $count;?> result(s) </p>
            </div>
            <h3> No result about <?php echo $name;?>. Please search another keyword. </h3>
        </main>
    <?php }?>

    <!-- footer element -->
    <footer>
        Copyright © 2019 AMY&KENZO
    </footer>

</body>
</html>
<script>
// content string in each label associated with a marker in the 
// label content show the pictures, name, url to detailed page and rating of each object
var contentStrings = []
<?php for($i=0;$i<$count;$i++){ ?>
    <?php
    // Verify valid access code
    $sql_pic = 'SELECT * from files WHERE object_id = :id';
    $id = $results[$i]["object_id"];
    $stms1 = $dbh->prepare($sql_pic);
    try {
        $stms1->execute(array("id"=>$id));
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $targets = $stms1->fetchAll();
    ?>
    var content = '<div class="single_object">'+
            '<div class="food-pic">'+
                    '<a href="individual_detail.php?object_id=<?php echo $results[$i]["object_id"] ?> ">'+
                        '<img src=<?php echo $targets[0]["path"];?> alt="opps">'+
                    '</a>'+
            '</div>'+
            '<div class="single_object_info">'+
                '<div class="object-name"> <a href="individual_detail.php?object_id=<?php echo $results[$i]["object_id"] ?> ">'+'<?php echo $results[$i]["business_name"]; ?>'+'</a></div>'+
                '<div class="object-rating">';
    if(<?php echo $results[$i]["rating"]; ?><1){ // the number of star checked (checked means light up the star) based on the rating of the object
        content += '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>';;
    }
    else if( 1<= <?php echo $results[$i]["rating"]; ?> && <?php echo $results[$i]["rating"]; ?> <2){
        content+='<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>';
    }
    else if(2<= <?php echo $results[$i]["rating"]; ?> && <?php echo $results[$i]["rating"]; ?> <3){
        content+='<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>';
    }
    else if(3<= <?php echo $results[$i]["rating"]; ?> && <?php echo $results[$i]["rating"]; ?> <4){
        content+='<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star"></span>'+
        '<span class="fa fa-star"></span>';
    }
    else if(4<= <?php echo $results[$i]["rating"]; ?> && <?php echo $results[$i]["rating"]; ?> <5){
        content+='<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star"></span>';
    }
    else{
        content+='<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>'+
        '<span class="fa fa-star checked"></span>';
    }
    content+='<span>'+ '<?php echo $results[$i]["rating"]; ?>' +'</span> '+'</div>'+'<div class="object-location">'+'<span>'+ '<?php echo $results[$i]["latitude"]; ?>'+ ', '+ '<?php echo $results[$i]["longitude"]; ?>' +'</span>'+'</div>'+'</div>'+'</div>';
    contentStrings[<?php echo $i;?>] = content;                  
<?php } ?>

// Initialize and add the map
function initMap() {
    //create an infowindow for displaying information associated with the markers
    const infowindow = new google.maps.InfoWindow();
    // centered at hamilton city
    var map = new google.maps.Map(document.getElementById('map'), {center: {lat: <?php echo $results[0]["latitude"];?>, lng: <?php echo $results[0]["longitude"];?>},zoom: 9});
    function placeMarker(loc,content){ //define palce Marker function
        const marker = new google.maps.Marker({position:loc,map:map});
        marker.addListener('click', function() {
            infowindow.close();// when click other label, the old opened label will be closed
            infowindow.setContent(content);  // set content into the label
            infowindow.open(map, marker);// open the new info_window
        });
    }
    <?php for($i=0;$i<$count;$i++){ ?>
        placeMarker({lat:<?php echo $results[$i]["latitude"];?>, lng:<?php echo $results[$i]["longitude"];?>},contentStrings[<?php echo $i;?>]); // place all the markers in the map
    <?php } ?>
}

</script>

