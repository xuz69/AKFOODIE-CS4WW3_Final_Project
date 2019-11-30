<?php 
    session_start();
    // Check if session is a logged in one, if it isn't then redirect to login.
    if (!isset($_SESSION['ID'])){
        header("Location: https://{$_SERVER['HTTP_HOST']}/login.html");
    }
?>

<!DOCTYPE html>
<html>
    <!-- head element -->
    <head>
        <title> Submission Page</title>
        
        <!-- control layout of the page on mobile brower -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.5">
        <!-- link to the external css file resource, "submission_style.css" is the same folder -->
        <link rel="stylesheet" type="text/css" href="submission_style.css">
        <!-- link to submission_geolocation.js -->
        <script src="submission_geolocation.js" defer></script>
    </head>
    <!-- body element -->
    <body>
        <!-- header element -->
        <!-- In header, it contains the logo and navigation menu -->
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

        <!-- container contains subtitles and submission form -->
        <div class="container">
            <div class = "central">
                <h2>Start Your New Business Here!</h2>
                <p> Please fill in the correct information and image for your business. Welcome to the group!</p>
            </div>
            <div class="submission-box">
                    <div class="subtitle">
                            <h4>Business Information</h4> <!-- label for a form -->
                    </div>
                    <div class="form-area">
                        <form action="submit.php" method="post" enctype="multipart/form-data"> <!-- validation using HTML5/CSS -->
                            <label for="businessname">Name of The Business *</label>
                            <input type="text" name="business" id="businessname" placeholder="Name" required> <!-- name of the business required-->
                            <label for="descriptionofbusiness">Description *</label> <!-- Description of the business required-->
                            <textarea id="descriptionofbusiness" rows="3" name="description" placeholder="Please write down some description of your business!" required></textarea> <!-- textarea used to enter the description of the business, required-->
                            <label>Upload Image *</label>
                            <input type="file" id="fileToUpload" name="fileToUpload[]" accept="image/png, image/jpeg, image/jpg" multiple required> <!-- a button used to upload multiple files but only images, at least one image and .png, .jpeg, .jpg are allowed -->
                            <label for="inputlatitude">Latitude *</label> 
                            <input type="number" id="inputlatitude" name="latitude" placeholder="Latitude" min="-90" max="90" step="any" required> <!-- Latitude of the business required-->
                            <label for="inputlongitude">Longitude *</label>
                            <input type="number" id="inputlongitude" name="longitude" placeholder="Longitude" min="-180" max="180" step="any" required> <!-- Longitude of the business required-->
                            <!--button: Search For Location"> -->
                            <input type="button" value="Search For Location" onclick="showlocation()"> <!-- call the showlocation function in submission_geolocation.js -->

                            <input type="submit" value="SUBMIT"> <!-- submission button -->
                        </form>
                    </div>
                </div>
        </div>
        <!-- footer element -->
        <footer>Copyright © 2019 AMY&KENZO</footer>
    </body>
</html>

