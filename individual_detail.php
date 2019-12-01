<?php

// individual detail page
session_start();

// connect to the server
$dbh = new PDO('mysql:host=localhost;dbname=akfoodie', 'root', 'BTS613forever-');

$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// GET Variable
$id_obj = (int)$_GET["object_id"];

// search the object from the object database
$sql = 'SELECT * FROM objects WHERE object_id = :id ';

// search the object from the reviews database
$sql2 = 'SELECT * FROM reviews WHERE object_id = :id ORDER BY date DESC, review_id DESC';

// Prepares statements for execution and return statement objects
$stms = $dbh->prepare($sql);
$stms2 = $dbh->prepare($sql2);
try {
    $stms->execute(array("id"=>$id_obj));
    $stms2->execute(array("id"=>$id_obj));
} catch (PDOException $e) {
    echo $e->getMessage();
}
// Returns an array containing all of the result set rows
$result = $stms->fetchAll();
$reviews = $stms2->fetchAll();
$count = count($reviews);

// rate star
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
        <span class="fa fa-star checked"></span>';
    }

}

?>
<!DOCTYPE html>
<html>
    <!-- head element -->
    <head>
        <title> Individual Sample </title>
        
        <!-- control layout of the page on mobile brower -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.5">

        <!-- link to the external css file resource, "sample_individual_page.css" is the same folder -->
        <link rel="stylesheet" type="text/css" href="sample_individual_page.css">
        <!-- this source is searched online because I need this to show the rating stars -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    
    <!-- body element -->
    <body>
        <!-- header element -->
        <header>
            <div class="logo">
                <!--the logo of the website is an image, clicking the logo will be linked to the search page (home page) -->
                <a href="search.html"><img src="ak.jpg" alt="" width = "50" height="50"></a>
            </div>

            <!--navigation menu-->
            <!-- There are five links to other pages, such as login page, sign up page, home page etc. -->
            <div class="top-navigation">
            	<a href="login.html"> Log In </a>
                <a href="registration.html">Sign Up</a>
                <a href="logout.php"> Log Out</a>
                <a href="submission.php"> New Business </a>
                <a href="search.html">Home</a>
            </div>
        </header>

        <!-- main content -->
        <main>
            <!-- object-pics is a containers to hold some pictures in a row -->
            <!-- four sample pictures of the restaurant listed -->
            <div class="object-pics">
                <?php 
                    // Verify valid access code
                    $sql_pic = 'SELECT * from files WHERE object_id = :id';
                    $pic = $dbh->prepare($sql_pic);
                    try {
                        $pic->execute(array("id"=>$id_obj));
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                    $targets = $pic->fetchAll();
                    $numPic = count($targets);
                ?>
                <?php for($i = 0; $i< 4; $i++){?>
                <!-- single-pic is an element that used to hold an image -->
                <div class="single-pic">
                    <?php if($i <= $numPic -1){?>
                    <img src=<?php echo $targets[$i]["path"];?> alt="opps">
                    <?php } else {?>
                    <img src="catpic.png" alt="opps">
                    <?php } ?>
                </div>
                <?php } ?>
                
            </div>

            <hr> <!-- a boundary to make the page more clear -->

            <!-- object-location class and object-infomation class are in the same row -->
            <!-- the precentage of width of two parts is described in css file -->
            <!-- object-location will hold a map -->
            <div class="object-location">
                <div id="map"></div>
                <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDl-wq_Axw2CMLmefmsz9VPC1sM0ITeHhI&callback=initMap" defer></script> <!-- Use the google map API and call the function in external js -->
            </div>
            <!-- object-information will contains all the information of this restaurant, such as name, ratings and some reviews-->
            <div class="object-information">
                <div class="info-name"> <!-- name of the restaurant-->
                    <h3> <?php echo $result[0]["business_name"]?> </h3>
                </div>

                <div class="info-ratings"> <!-- ratings of the restaurant-->
                    <div>
                        <?php star($result[0]["rating"]);?>
                        <span> <?php echo $result[0]["rating"]; ?> </span>
                        <span> <?php echo $count; ?> reviews</span> <!-- number of reviews-->
                    </div>
                </div>
                <!-- a form provide a textarea for user to write reviews and submit by the submit button -->
                <div class="info-write-reviews">
                    <form action="addComments.php?user_id=<?php echo $_SESSION['ID'];?>&object_id=<?php echo $id_obj; ?>" method="post">
                        <label> Rating: </label>
                        <select id="rating" name="rating"> <!--selection element for user to choose their nationality-->
                            <option value="0"> 0 </option>
                            <option value="1">  1 </option>
                            <option value="2"> 2 </option>
                            <option value="3"> 3 </option>
                            <option value="4">  4 </option>
                            <option value="5"> 5 </option>
                        </select>
                        <textarea id="comment" name="comment" rows="6" cols="100" placeholder="Comments Here..." required></textarea>
                        <input type="submit" value="SUBMIT">
                    </form>   
                </div>

                <!--using tabular element to show all the reviews-->
                <div class="info-reviews">
                    <div class="reviews-label">
                        <h3>All reviews: <?php echo $count . " review(s)";?></h3>
                    </div>
                    <table>
                        <?php for($i=0;$i<$count;$i++){ ?>
                        <tr>
                            <th><!-- in each table element, it contains a user pic, ratings, posted date and the content of the review-->
                                <!-- user-pic and user-comment_info is in the same row-->
                                <div class="user-pic"><!-- user pic-->
                                    <img src="user-photo.png" alt="ops">
                                </div>
                                <div class="user-comment-info">
                                    <div class="user-ratings"><!-- user ratings-->
                                        <div>
                                            <?php star($reviews[$i]["rating"]);?>
                                            <span> <?php echo $reviews[$i]["rating"]; ?> </span> <!--ratings-->
                                        </div>

                                        <div> 
                                            <span> <?php echo $reviews[$i]["date"]; ?> </span> <!-- date -->
                                        </div>
                                    </div>

                                    <div class="user-comment">
                                        <p> <?php echo $reviews[$i]["content"]; ?> </p> <!--paragraphs of comments-->
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

        </main>
        <!--footer element-->
        <footer>
            Copyright © 2019 AMY&KENZO
        </footer>

    </body>
</html>

<script>
// The location of Burge
var burger = {lat:<?php echo $result[0]["latitude"];?>, lng:<?php echo $result[0]["longitude"];?>}
// Content of the Label, show the location of the restaurant to the users
var content = '<div class="map-label"> <p> The location is ( '+ burger['lat'] + ' , '+ burger['lng'] +' ).</p> </div>';                        
// Initialize and add the map
function initMap() {
    const infowindow = new google.maps.InfoWindow();
    // The map, centered at Burger
    var map = new google.maps.Map(
        document.getElementById('map'), {zoom: 9, center: burger});
    // The marker, positioned at burger
    var marker = new google.maps.Marker({position: burger, map: map});
    marker.addListener('click', function(){ // info_window show up after click the marker
        infowindow.setContent(content); // set content into the label
        infowindow.open(map, marker); // open the info_window
    });
}
</script>


