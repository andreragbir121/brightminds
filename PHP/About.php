<?php
session_start();
require_once "dbase_connect.php";


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../Index.php");;
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title>About BrightMinds</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>

<!-- about page used from previous class ITEC 240 : Webpage designs. 
Code can be found on github https://github.com/andreragbir121/Late-Bites  -->

<body>     
    
    <nav class="navbar">

    <ul class="Navigation">
       <li><img class ="navbar-logo" src ="../IMGS/LOGO/Navbar-Logo.png" alt=""></li> 
        <li class="nav-option" ><a class ="nav-links" href="../Index.php">Home</a></li>
        <li class="nav-option"><a class ="nav-links" href="About.php">About</a></li>
        <li class="nav-option"><a class ="nav-links" href="../PHP/EssayList.php">Essays</a></li>
        <li class="nav-option"><a class ="nav-links" href="Contact.php">Contact</a></li>
   <?php if (isset($_SESSION['userType']) && $_SESSION['userType'] === 'instructor') {
            echo '<li class="nav-option"><a class="nav-links" href="UngradedEssays.php">Ungraded Essays</a></li>';
        }
        ?>
        </ul>
        <div class="profile-dropdown">
            <div class="profile-icon">
                <?php if (!empty($_SESSION['pfp'])) {
                    echo '<img src="'.($_SESSION['pfp']).'" alt="Profile">';
                } else {
                    echo '<img src="IMGS/Profile-pictures/avatar1.png" alt="Profile">';
                }
                
?>           
            </div>
            <div class="profile-selection">
                <a class="profile-options">Profile</a>
                <a class="profile-options" href="">Preference</a>
                <?php if (isset($_SESSION['username'])) { 
                    echo '<a class="profile-options" href="?logout=1">Logout</a>';          //Logout option code sampled from: https://stackoverflow.com/questions/12209438/logout-button-php
                } else { 
                    echo '<a class="profile-options" href="#login">Login</a>';
                } ?>
                </div>
            </div>
    </nav>

    
    
    <div class="about">
        <!-- Image by Gordon Johnson from Pixabay src = https://pixabay.com/vectors/design-book-education-candles-7647692/ -->
		<img class="about-img" src = "../IMGS/Other/design-7647692_1280.png" alt="Drawing of a book, black and white to blend with page background">
        <div class="about-txt">
            <h2 class ="about-heading">BrightMinds</h2>
            <h5 class="about-sub-heading">Ministry of education</h5>
            <p class="about-description">BrightMinds is a website dedicated to promoting and presenting to the public the creative minds of our next generation. The main purpose is to allow everyone throughout the world to view and explore different essays written by students from secondary schools in Trinidad and Tobago, as well as their grades and remarks from our local instructors at each institution. The website is open to the public with no need to register. <br> <br>
		Lorem ipsum dolor, sit amet consectetur adipisicing elit. Totam labore sunt officia non modi repellendus fuga, numquam aperiam! Exercitationem deleniti, rem velit et nihil cumque fugiat corporis quis eum delectus.	
        </p>  
		</div>

    </div>
</body>
</html>