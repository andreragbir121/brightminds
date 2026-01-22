<?php
session_start();
require_once "dbase_connect.php";


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../Index.php");;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Essay Listing Page</title>    
    <script src="https://kit.fontawesome.com/59b21b487b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

    <nav class="navbar">
        <ul class="Navigation">
           <li><img class="navbar-logo" src="../IMGS/LOGO/Navbar-Logo.png" alt=""></li> 
            <li class="nav-option"><a class="nav-links" href="/index.php">Home</a></li>
            <li class="nav-option"><a class="nav-links" href="About.php">About</a></li>
            <li class="nav-option"><a class="nav-links" href="EssayList.php">Essays</a></li>
            <li class="nav-option"><a class="nav-links" href="Contact.php">Contact</a></li>
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
    
    <h1 class="Essay-heading">Essay Listing of Students</h1>
    <h2 class="essay-list">Public Essays</h2>   

    <div class="top-essays">
    <?php
if (isset($_SESSION['userType'])) {
        if ($_SESSION['userType'] === 'instructor') {
            $query = 'select * from essaylist order by essayRating desc';
        } else{
            $query = 'select * from essaylist where isPublic = 1 order by essayRating desc';
        } 
    }else {
            $query = 'select * from essaylist where isPublic = 1 order by essayRating desc';
        }
    
    $result = null; 
    try { 
    $result = mysqli_query($conn, $query);  
    } catch (Exception $e){ 
    echo '<br><br>Error occurred: ' . mysqli_error($conn) . '<br><br>'; 
    echo "Please <a href=\"..\index.html\">return to form</a> to resubmit";  
    }

    
    if ($result) {  
        if (mysqli_num_rows($result) > 0) { 
            while ($row = mysqli_fetch_assoc($result)) {                 
                echo "
                <div class='essay'>
                    <p class='essay-info'>Username: <span>{$row['username']}</span></p>
                    <p class='essay-info'>Student name: <span>{$row['studentName']}</span></p>
                    <p class='essay-info'>Essay Title: <span>{$row['essayTitle']}</span></p>
                    <p class='essay-info'>Essay Rating: <span>{$row['essayRating']}</span></p>
                    <p class='essay-info'>Grade: <span>{$row['grade']}</span></p>
                    <p class='essay-info'>First Paragraph: <span>{$row['essayFirstParagraph']}</span></p>

                    
                    <a href=\"essayDetails.php?essayID={$row['essayID']}\" class='view-essay'>View</a>
                </div>  ";
            }
        } else {
            echo "<br>Query executed. No records found ."; 
        }
    } 

    if (isset($_SESSION['userType']) && $_SESSION['userType'] === 'student') {

    echo "<div class='essay'>
     <a class = 'essay-add' href='EssayUpload.php'>
     <i class='essay-add fa-sharp fa-solid fa-plus'></i>
     <span>Add Essay</span>
     </a>
     </div>";
    }
    mysqli_close($conn);
    ?>
</body>
</html>