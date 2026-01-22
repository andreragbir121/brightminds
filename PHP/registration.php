<?php
session_start();
require_once "dbase_connect.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>    
    <script src="https://kit.fontawesome.com/59b21b487b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../CSS/style.css">

</head>
<body>
    
<nav class="navbar">

    <ul class="Navigation">
       <li><img class ="navbar-logo" src ="../IMGS/LOGO/Navbar-Logo.png" alt=""></li> 
        <li class="nav-option" ><a class ="nav-links" href="/index.php">Home</a></li>
        <li class="nav-option"><a class ="nav-links" href="About.html">About</a></li>
        <li class="nav-option"><a class ="nav-links" href="../PHP/EssayList.php">Essays</a></li>
        <li class="nav-option"><a class ="nav-links" href="Contact.html">Contact</a></li>
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
                    echo '<img src="../IMGS/Profile-pictures/avatar1.png" alt="Profile">';
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


// ===================================================================================================================================
  // Data Validation Part: 

$fullName = $username = $birthDate = $parentName = $parentEmail = $schoolName = $classLevel = $passwordConfirm = "";
$fullNameErr = $usernameErr = $birthDateErr = $parentNameErr = $parentEmailErr = $schoolNameErr = $classLevelErr = $passwordErr = $passwordConfirmErr = "";

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

  
  if (empty($_POST["username"])) {

    $usernameErr = "A username is required";
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
 
if (empty($_POST["birthDate"])) {

  $birthDateErr = "Birth Date is required";
  $valid = false;
}
else {
  $birthDate = $_POST["birthDate"];
  $dateOfBirth = test_input($birthDate);

  if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $birthDate)) {
      $birthDateErr = "username may only contain letters, numbers and special characters. No spaces";
      $valid = false;
  }
}


if (empty($_POST["parentName"])) {

  $parentNameErr = "Parent full name is required";
  $valid = false;
}
else {
  $parentName = $_POST["parentName"];
  $parentName = test_input($parentName); 

  if (!preg_match("/[a-zA-Z]+[ a-zA-Z]*/", $parentName)) {
      $parentNameErr = "Name may only contain letters or ' ! and -";
      $valid = false;
  }
}

if (empty($_POST["parentEmail"])) {

  $parentEmailErr = "Parent email is required";
  $valid = false;
}
else {
  $parentEmail = $_POST["parentEmail"];
  $parentEmail = test_input($parentEmail);

  if (!preg_match("/^[a-zA-Z0-9]{3,24}@[ a-zA-Z0-9]{2,40}.[a-zA-Z]{2,4}$/", $parentEmail)) {
      $parentEmailErr = "email can only contain letters and special char";
      $valid = false;
  }
}


if (empty($_POST["schoolName"])) {
    $schoolNameErr = "School selection is required";
    $valid = false;
} else {
    $schoolName = test_input($_POST["schoolName"]);
}


if (empty($_POST["classLevel"])) {
    $classLevelErr = "Class level is required";
    $valid = false;
} else {
    $classLevel = test_input($_POST["classLevel"]);
}

if (empty($_POST["classLevel"])) {
  $classLevelErr = "Class level is required";
  $valid = false;
} else {
  $classLevel = test_input($_POST["classLevel"]);
}


if (empty($_POST["password"])) {

  $passwordErr = "password is required";
  $valid = false;
}
else {
  $password = $_POST["password"];
  $password = test_input($password);

  // Regex for password taken from : https://uibakery.io/regex-library/password 
  if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/", $password)) {
      $passwordErr = "Password must be at least 8 characters long and include an uppercase letter, lowercase letter, number, and symbol";
      $valid = false;
  }
}

if (empty($_POST["passwordConfirm"])) {
    $passwordConfirmErr = "Please confirm your password";
    $valid = false;
} else {
    $passwordConfirm = $_POST["passwordConfirm"];
    $passwordConfirm = test_input($passwordConfirm);
    
    if ($password !== $passwordConfirm) {
        $passwordConfirmErr = "Passwords do not match";
        $valid = false;
    }
}
// if(!($password===$passwordConfirm))die("The passwords do not Match.  Please return to registration page");

// Image Uploading: 
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["pfp"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["pfp"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check file size
if ($_FILES["pfp"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["pfp"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( $_FILES["pfp"]["name"])). " has been uploaded.";
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}

    $pfp = $_POST && $_FILES["pfp"];

    $password = password_hash ($password, PASSWORD_DEFAULT);

    $qry = "INSERT INTO student (pfp, fullName, username, birthDate, parentName, parentEmail, password, schoolName, classLevel) VALUES ('$pfp', '$fullName', '$username', '$birthDate', '$parentName', '$parentEmail', '$password', '$schoolName', '$classLevel')";

    $result = null;

    try{
        $result = mysqli_query($conn, $qry);

    } catch(Exception $e) {
        echo '<br><br>Error occurred: ' . mysqli_error($conn) . '<br><br>';
        echo " Failed to register. Please try again";
    }

    if($result) echo "<br><br>Thank Your for registering. <a href=\'../index.php'>return to home</a> to continue<br><br>";
  }

  function test_input($data)
  {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
  }

  // ===================================================================================================================================

?>

    <div class="signup-form">
        <h2 class="form-heading">Sign Up</h2>
        <h4 class="reg-instruction">It's simple and easy</h4>
        <img class="reg-logo" src="../IMGS/LOGO/Logo.png" alt="">

        <form class = "account-info" method = "POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" onsubmit="return validate()">

          <!-- inserting on image file https://stackoverflow.com/questions/3828554/how-to-allow-input-type-file-to-accept-only-image-files -->
          <input class="pfp" type="file" id = "pfp" name = "pfp">
        

          <input class="account-input" id="fullName" name="fullName" type="text" placeholder="Full Name" value="<?php echo $fullName; ?>"/><br>
          <span id="fullNameErr" class="error"><?php echo $fullNameErr; ?></span>


          <input  class="account-input" id="username" name="username" type="text" placeholder="username" value="<?php echo $username; ?>"/><br>
          <span id="usernameErr" class="error"><?php echo $usernameErr; ?></span>

          
          <input class="account-input" id="birthDate" name = "birthDate" type="text" placeholder ="Date of Birth" onfocus="(this.type = 'date')"value="<?php echo $birthDate; ?>"/>
          <span id="birthDateErr" class="error"><?php echo $birthDateErr; ?></span>


          <input class="account-input" id="parentName" name="parentName" type="text" placeholder="Parent Full Name" value="<?php echo $parentName; ?>"/><br>
          <span id="parentNameErr" class="error"><?php echo $parentNameErr; ?></span>


          <input class="account-input" id="parentEmail" name="parentEmail" type="email" placeholder="Parent Email" value="<?php echo $parentEmail; ?>"/><br>
          <span id="parentEmailErr" class="error"><?php echo $parentEmailErr; ?></span>
          
          
          <select class="account-input account-selection" name="schoolName" id="schoolName" title="schoolName" value="<?php echo $schoolName; ?>"/><br>
            <option value="" disabled selected>School</option>
              <option value="Penal_Secondary_School">Penal Secondary School</option>
              <option value="Shiva_Boys_Hindu_College">Shiva Boys Hindu College</option>
              <option value="Iere_High_School">Iere High School</option>
              <option value="Debe_High_School">Debe High School</option>
          </select>
          <span id="schoolNameErr" class="error"><?php echo $schoolNameErr; ?></span>

          <select  class="account-input account-selection" title="classLevel" name="classLevel" id="classLevel" value="<?php echo $classLevel; ?>"/><br>
            <option value="" disabled selected>Class Level</option>
              <option value="form1">Form 1</option>
              <option value="form2">Form 2</option>
              <option value="form3">Form 3</option>
              <option value="form4">Form 4</option>
              <option value="form5">Form 5</option>
          </select>
          <span id="classLevelErr" class="error"><?php echo $classLevelErr; ?></span>

        
          <input class="account-input" id="password" name="password" type="password" placeholder="password"value="<?php echo $password; ?>"/><br>
          <span id="passwordErr" class="error"><?php echo $passwordErr; ?></span>

          <input class="account-input" id="passwordConfirm" name="passwordConfirm" type="password" placeholder="Confirm Password"value="<?php echo $passwordConfirm; ?>"/><br>
          <span id="passwordErr" class="error"><?php echo $passwordConfirmErr; ?></span>


          <p class="terms-and-conditions" >By registering I agree that I have read the <a href="../DOCS/terms-and-conditions-template.pdf">terms and condition</a></p>


          <input type="submit" name="submit" class="submit-btn">
        </form>

        <p class="existing-account">Already have an account? <br> <a href="../index.php">Login here</a></p>

      </div>
      <script type="text/javascript" src="../JS/RegValidations.js"></script>

</body>
</html>