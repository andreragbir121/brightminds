<?php 
session_start();
    require_once 'PHP/dbase_connect.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: Index.php");;
}

$message = "";
if (isset($_SESSION['username'])) {
    $message = "<div>
    Welcome back, ".($_SESSION['fullName'])." 
        </div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
     <!--Coat of arms taken from trinidadexpress and adjusted for better quality through Adobe photoshop
    https://trinidadexpress.com/news/local/proud-moment-as-new-look-coat-of-arms-unveiled/article_cb95ade6-d6d3-11ef-a414-8b6d4118897d.html -->


   <!-- Horizontal bar sample taken from W3Schools, and adjusted according to insert the logo and profile icon:
   CSS Horizontal Navigation Bar: https://www.w3schools.com/css/css_navbar_horizontal.asp#gsc.tab=0&gsc.q=increase%20size%20of%20input%20box%20css -->
    <nav class="navbar">

        <ul class="Navigation">
           <li><img class ="navbar-logo" src ="IMGS/LOGO/Navbar-Logo.png" alt=""></li> 
            <li class="nav-option" ><a class ="nav-links" href="index.php">Home</a></li>
            <li class="nav-option"><a class ="nav-links" href="PHP/About.php">About</a></li>
            <li class="nav-option"><a class ="nav-links" href="PHP/EssayList.php">Essays</a></li>
            <li class="nav-option"><a class ="nav-links" href="PHP/Contact.php">Contact</a></li>
            <?php if (isset($_SESSION['userType']) && $_SESSION['userType'] === 'instructor') {
            echo '<li class="nav-option"><a class="nav-links" href="PHP/UngradedEssays.php">Ungraded Essays</a></li>';
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
                
?>            </div>
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
        <?php

        // Login validations: 
		$username = $password = "";
		$usernameErr = $passwordErr = "";
        $userType = 'student';
		$valid = true;

		if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["submit"])) {

        if (isset($_POST['userType'])) {
        if ($_POST['userType'] == 'student') {
            $userType = 'student';
        } else {
            $userType = 'instructor';
        }
    }

		if (empty($_POST["username"])) {
			$usernameErr = "username is required";
			$valid = false;
		}
		else {
			$username = $_POST["username"];
			$username = test_input($username);
		
			if (!preg_match("/^[a-zA-Z0-9_\-!@#$%^&*()+=.,;:]+$/", $username)) {
				$usernameErr = "username may only contain letters, numbers and special characters. No spaces";
				$valid = false;
			}
		}

		
    if (empty($_POST["password"])) {

	    $passwordErr = "password is required";
	    $valid = false;
    }
    else {
	    $password = $_POST["password"];
	    $password = test_input($password);
  
  }

		// $username = $_POST['username'];
		// $password = $_POST['password'];
			
        if ($valid) {
        if ($userType == 'student') {
            $query = "select * from student where username = '$username'";
        } else {
            $query = "select *from instructor where username = '$username'";
        }
    
		$result = null; 

		try{
            if (!empty($query)){
			$result = mysqli_query($conn, $query);
            }

        }catch(Exception $e){
			echo "there was an error with you login. Please try again";
		}
		
		if ($result){
			if (mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_assoc($result);
                $login_success = password_verify($password, $row['password']);

				if ($login_success){
                    if ($login_success){
                        $_SESSION['userType'] = $userType;
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['fullName'] = $row['fullName'];
                        
                        if ($userType == 'student') {
                            $_SESSION['pfp'] = $row['pfp'];
                            $_SESSION['birthDate'] = $row['birthDate'];
                            $_SESSION['parentName'] = $row['parentName'];
                            $_SESSION['parentEmail'] = $row['parentEmail'];
                            $_SESSION['schoolName'] = $row['schoolName'];
                            $_SESSION['classLevel'] = $row['classLevel'];
                        
                        } else {
                            $_SESSION['pfp'] = $row['pfp'];
                            $_SESSION['instructorID'] = $row['instructorID'];
                            $_SESSION['email'] = $row['email'];
                            $_SESSION['schoolName'] = $row['schoolName'];
                        }
					header("Location: Index.php"); //issues logging in so header was solution, user need to click login twice - https://stackoverflow.com/questions/39291500/redirecting-user-after-login-with-headerlocation-is-not-working
		        }
        else echo "Invalid Credentials. Please try again";
    }
    else echo "Invalid Credentials. Please Try again";
}
    }
}
}
    
  function test_input($data)
  {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
  }		
        if (empty($message)) {
	?>

<form  class="login-form" method = "post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validate()">

<!-- Code used to check if radio is seleccted: https://stackoverflow.com/questions/4554758/how-to-read-if-a-checkbox-is-checked-in-php -->
 <div class="userType">
        Student <input type="radio" name="userType" value="student" <?php echo ($userType == 'student') ? 'checked' : ''; ?>>
        Instructor <input type="radio" name="userType" value="instructor" <?php echo ($userType == 'instructor') ? 'checked' : ''; ?>>

    </div>

<div class="credentials">
<input  class="login-info" id="username" name="username" type="text" placeholder="username" value="<?php echo $username; ?>"/><br>
<span id="usernameErr" class="error"><?php echo $usernameErr; ?></span>


<input class="login-info" id="password" name="password" type="password" placeholder="password"value="<?php echo $password; ?>"/><br>
<span id="passwordErr" class="error"><?php echo $passwordErr; ?></span>
</div>

<div class="remember-me">
<input class="rem-checkbox" type="checkbox" id="remember-me">
<label class="rem-text" for="remember-me">Remember me</label>
</div>


<input type="submit" class="login-btn" id = "submit" name="submit" value="Login"/>
<a class="forgot-psw" href="">Forgot your password?</a>

<p class = "register" >Don't have an account yet?<a href="PHP/registration.php" class="register-link"> Register</a></p>

</form>

 <?php
    } else {
        echo $message;
    }
    ?>

<img class = "logo" src="IMGS/LOGO/Logo.png" alt="Ministry Of education Logo">


<p class="short-intro">BrightMinds<br><span>The bright minds of our next generation</span></p>

<!-- Essays taken from essay page and uses same CSS as the essay page -->
<h2 class="featured-list">Featured Essays</h2>   

<div class="top-essays">

<?php
    $query = "select * from essaylist where isPublic = 1 order by essayRating desc limit 3 ";
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

                    
                    <a href=\"PHP/essayDetails.php?essayID={$row['essayID']}\" class='view-essay'>View</a>
                </div>  ";
            }
        } else {
            echo "<br>Query executed. No records found ."; 
        }
    } 
        	mysqli_close($conn);

    ?>
</div>

<script type="text/javascript" src="JS/validations.js"></script>

</body>
</html>