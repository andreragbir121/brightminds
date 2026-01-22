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
        <li class="nav-option"><a class ="nav-links" href="About.php">About</a></li>
        <li class="nav-option"><a class ="nav-links" href="EssayList.php">Essays</a></li>
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
// Initialize variables
$fullName = $username = $birthDate = $parentName = $parentEmail = $schoolName = $classLevel = $passwordConfirm = "";
$fullNameErr = $usernameErr = $birthDateErr = $parentNameErr = $parentEmailErr = $schoolNameErr = $classLevelErr = $passwordErr = $passwordConfirmErr = "";
$valid = true;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {

    // Full Name
    if (empty($_POST["fullName"])) {
        $fullNameErr = "Full name is required";
        $valid = false;
    } else {
        $fullName = test_input($_POST["fullName"]);
        if (!preg_match("/^[a-zA-Z\s'-]+$/", $fullName)) {
            $fullNameErr = "Name may only contain letters, spaces, apostrophes, and hyphens";
            $valid = false;
        }
    }

    // Username
    if (empty($_POST["username"])) {
        $usernameErr = "A username is required";
        $valid = false;
    } else {
        $username = test_input($_POST["username"]);
        if (!preg_match("/^[a-zA-Z0-9_\-!@#$%^&*()+=.,;:]+$/", $username)) {
            $usernameErr = "Invalid characters in username";
            $valid = false;
        }
    }

    // Birth Date
    if (empty($_POST["birthDate"])) {
        $birthDateErr = "Birth Date is required";
        $valid = false;
    } else {
        $birthDate = test_input($_POST["birthDate"]);
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $birthDate)) {
            $birthDateErr = "Invalid date format (YYYY-MM-DD)";
            $valid = false;
        }
    }

    // Parent Name
    if (empty($_POST["parentName"])) {
        $parentNameErr = "Parent full name is required";
        $valid = false;
    } else {
        $parentName = test_input($_POST["parentName"]);
        if (!preg_match("/^[a-zA-Z\s'-]+$/", $parentName)) {
            $parentNameErr = "Invalid characters in parent name";
            $valid = false;
        }
    }

    // Parent Email
    if (empty($_POST["parentEmail"])) {
        $parentEmailErr = "Parent email is required";
        $valid = false;
    } else {
        $parentEmail = test_input($_POST["parentEmail"]);
        if (!filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
            $parentEmailErr = "Invalid email format";
            $valid = false;
        }
    }

    // School Name
    if (empty($_POST["schoolName"])) {
        $schoolNameErr = "School selection is required";
        $valid = false;
    } else {
        $schoolName = test_input($_POST["schoolName"]);
    }

    // Class Level
    if (empty($_POST["classLevel"])) {
        $classLevelErr = "Class level is required";
        $valid = false;
    } else {
        $classLevel = test_input($_POST["classLevel"]);
    }

    // Password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
        $valid = false;
    } else {
        $password = test_input($_POST["password"]);
        if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/", $password)) {
            $passwordErr = "Password must be at least 8 characters long and include uppercase, lowercase, number, and symbol";
            $valid = false;
        }
    }

    // Confirm Password
    if (empty($_POST["passwordConfirm"])) {
        $passwordConfirmErr = "Please confirm your password";
        $valid = false;
    } else {
        $passwordConfirm = test_input($_POST["passwordConfirm"]);
        if ($password !== $passwordConfirm) {
            $passwordConfirmErr = "Passwords do not match";
            $valid = false;
        }
    }

    // File Upload
    $pfp = "";
    if (!empty($_FILES["pfp"]["name"])) {
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["pfp"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["pfp"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $valid = false;
        }

        if ($_FILES["pfp"]["size"] > 500000) {
            echo "File too large.";
            $valid = false;
        }

        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo "Invalid file type.";
            $valid = false;
        }

        if ($valid && move_uploaded_file($_FILES["pfp"]["tmp_name"], $target_file)) {
            $pfp = "uploads/" . basename($_FILES["pfp"]["name"]);
        } else {
            echo "Error uploading file.";
            $valid = false;
        }
    }

    // Insert into DB
    if ($valid) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO student (pfp, fullName, username, birthDate, parentName, parentEmail, password, schoolName, classLevel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $pfp, $fullName, $username, $birthDate, $parentName, $parentEmail, $passwordHash, $schoolName, $classLevel);

        if ($stmt->execute()) {
            echo "Thank you for registering. <a href='/index.php'>Return to home</a>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
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
      <script type="text/javascript" src="/JS/RegValidations.js"></script>

</body>
</html>