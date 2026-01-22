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
    <title>Contact Page</title>    
    <script src="https://kit.fontawesome.com/59b21b487b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../CSS/style.css">


</head>
<body>
  <nav class="navbar">

    <ul class="Navigation">
        <li><img class ="navbar-logo" src ="../IMGS/LOGO/Navbar-Logo.png" alt=""></li> 
         <li class="nav-option" ><a class ="nav-links" href="/index.php">Home</a></li>
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

    
    <div class="php-container">
<?php
//connecting to dbase
require_once "dbase_connect.php";


$fullName = $Email = $phNumber = $comment = "";
$fullNameErr = $EmailErr = $phNumberErr = $commentErr = "";

$valid = true;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["submit"])) {
    

    if (empty($_POST["fullName"])) {

        $fullNameErr = "Full name is required";
        $valid = false;
    }
    else {
        $fullName = $_POST["fullName"];
        $fullName = test_input($fullName); 

        if (!preg_match("/[a-zA-Z]+[ a-zA-Z]*/", $fullName)) {     
            $fullNameErr = "Name may only contain letters or ' ! and -";
            $valid = false;
        }
    }

    

    if (empty($_POST["Email"])) {

        $Email = "Email is required";
        $valid = false;
      }
      else {
        $Email = $_POST["Email"];
        $Email = test_input($Email);
      
        if (!preg_match("/^[a-zA-Z0-9]{3,24}@[ a-zA-Z0-9]{2,40}.[a-zA-Z]{2,4}$/", $Email)) {
            $EmailErr = "Email can only contain letters and special char";
            $valid = false;
        }
      }


      if (empty($_POST["phNumber"])) {

        $phNumber = "Phone number is required";
        $valid = false;
      }
      else {
        $phNumber = $_POST["phNumber"];
        $phNumber = test_input($phNumber);
      
        if (!preg_match("/^[0-9]{10,15}$/", $phNumber)) {
            $phNumberErr = "Phone number must be valid";
            $valid = false;
        }
      }

      if (empty($_POST["comment"])) {

        $comment = "Comment is required";
        $valid = false;
      }
      else {
        $comment = $_POST["comment"];
        $comment = test_input($comment);
      
        if (!preg_match("/^.{10,}$/", $comment)) {
            $commentErr = "Comment must be a minimum of 10 characters in length";
            $valid = false;
        }
      }



 $qry = "insert into contact (fullName, Email, phNumber, comment) values ('$fullName', '$Email', '$phNumber', '$comment')";

    $result = null;

    try{
        $result = mysqli_query($conn, $qry);

    } catch(Exception $e) {
        echo '<br><br>Error occurred: ' . mysqli_error($conn) . '<br><br>';
        echo "Please <a href=\"index.html\">return to form</a> to resubmit";
    }

    if($result) echo '<br><br>Thank You for your feedback.<br><br>';
}


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

</div>

    <h1 class = "contact-heading">Contact</h1>
    <h2 class = "contact-sub-heading-1">Have Questions</h2>
    <p class = "contact-sub-heading-2">We'd Love to Hear from You</p>

    
    <!--Contact form created by splitting the screen in half with the input section on the left, and contact details to the right. Below is guided link to split screen on W3Schools 
    Splitting contact screen <a href="https://www.w3schools.com/howto/howto_css_split_screen.asp-->

<div class="contact contact-input centered">
    <form method="POST" class="contact-form" name = "contact" id="contact" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validate()">
        
        <input class="contact-input" id="fullName" name="fullName" type="text" placeholder="Full Name" value="<?php echo $fullName; ?>"/>
        <span id="fullNameErr" class="error"><?php echo $fullNameErr; ?></span>


        <input class="contact-input" id = "Email" name="Email" type="Email" placeholder="Email" value="<?php echo $Email; ?>"/>
        <span id="EmailErr" class="error"><?php echo $EmailErr; ?></span>


        <input class="contact-input" id = "phNumber" name="phNumber" type="tel" placeholder="Phone Number" value="<?php echo $phNumber; ?>"/><br>   <!--W3Schools.com. (n.d.). https://www.w3schools.com/Tags/att_input_type_tel.asp-->
        <span id="phNumberErr" class="error"><?php echo $phNumberErr; ?></span>


        <textarea class="contact-input" id = "comment" name="comment" rows="6" cols="70" placeholder="Please Leave your Feedback here" value="<?php echo $comment; ?>"/></textarea><br>
        <span id="commentErr" class="error"><?php echo $commentErr; ?></span>

        <input type="submit" name="submit" class="contact-submit-btn">

    </form>
</div>


<div class="contact contact-details centered">
    <p class="contact-info"><i class="fa-solid fa-location-dot"></i>Port Of Spain</p>
    <p class="contact-info"><i class="fa-solid fa-phone"></i>+1-(868)-326-2313</p>
    <p class="contact-info"> <i class="fa-solid fa-envelope"></i>brightminds@gmail.com</p>

    <div class="social-media">
        
        <i class="fa-brands fa-facebook"></i>
        <i class="fa-brands fa-x-twitter"></i>
        <i class="fa-brands fa-linkedin"></i>
        <i class="fa-brands fa-instagram"></i>
        <i class="fa-brands fa-tiktok"></i>
        <p>Connect with our social media accounts</p>
    </div>
</div>  

<script type="text/javascript" src="../JS/contactValidation.js"></script>

</body>
</html>