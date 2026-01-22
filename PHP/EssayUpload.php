<?php
session_start();
require_once "dbase_connect.php";


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /index.php");;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Essay Uploading</title>    
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

// ===================================================================================================================================
  // Data Validation Part: 

$essayID = $username = $studentName = $essayTitle = $essayRating = $essayFirstParagraph = $schoolName = $classLevel = $essayDate = $fullEssay = "";
$essayIDErr = $usernameErr = $studentNameErr = $essayTitleErr = $essayRatingErr = $essayFirstParagraphErr = $schoolNameErr = $classLevelErr = $essayDateErr = $fullEssayErr = "";

$valid = true;

    if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['submit'])) {

        
    $isPublic = $_POST ['isPublic'];

  if (empty($_POST['essayID'])) {

    $essayIDErr = 'An essay ID is required for identifying your essay';
    $valid = false;
}
else {
    $essayID = $_POST['essayID'];
    $essayID = test_input($essayID);

    if (!preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', $essayID)) {
        $essayIDErr = 'Essay ID must contain a minimum of 1 letter and 1 number (NO SPECIAL CHARACTERS)';
        $valid = false;
    }
}
        
  if (empty($_POST['username'])) {

    $usernameErr = 'A username is required';
    $valid = false;
}
else {
    $username = $_POST['username'];
    $username = test_input($username);

    if (!preg_match('/^[a-zA-Z0-9_\-!@#$%^&*()+=.,;:]+$/', $username)) {
        $usernameErr = "username may only contain letters, numbers and special characters. No spaces";
        $valid = false;
    }
}


      if (empty($_POST['studentName'])) {

        $studentNameErr = 'Student full name is required';
        $valid = false;
    }
    else {
        $studentName = $_POST['studentName'];
        $studentName = test_input($studentName); 

        if (!preg_match('/[a-zA-Z]+[ a-zA-Z]*/', $studentName)) {     
            $studentNameErr = "Name may only contain letters or and spaces";
            $valid = false;
        }
    }

    if (empty($_POST['essayTitle'])) {

      $essayTitleErr = 'Essay title is required';
      $valid = false;
  }
  else {
      $essayTitle = $_POST['essayTitle'];
      $essayTitle = test_input($essayTitle); 

      if (!preg_match('/^[a-zA-Z0-9\W]{3,}$/', $essayTitle)) {     
          $essayTitleErr = 'Essay title must be a minimum of 3 characters in length';
          $valid = false;
      }
  }

    if (empty($_POST['essayRating'])) {

      $essayRatingErr = 'Essay Rating is required';
      $valid = false;
  }
  else {
      $essayRating = $_POST['essayRating'];
      $essayRating = test_input($essayRating); 

      if (!preg_match("/^[0-9]$/", $essayRating)) {     
          $essayRatingErr = 'Essay rating can only be a single digit between 0 - 9';
          $valid = false;
      }
  }

  
    if (empty($_POST['essayFirstParagraph'])) {

      $essayFirstParagraphErr = 'Essay Rating is required';
      $valid = false;
  }
  else {
      $essayFirstParagraph = $_POST['essayFirstParagraph'];
      $essayFirstParagraph = test_input($essayFirstParagraph); 

      if (!preg_match('/^[a-zA-Z0-9\W]{50,}$/', $essayFirstParagraph)) {     
          $essayFirstParagraphErr = 'The first paragraph must be at least 50 characters in length';
          $valid = false;
      }
  }


if (empty($_POST['schoolName'])) {
    $schoolNameErr = 'School selection is required';
    $valid = false;
} else {
    $schoolName = test_input($_POST['schoolName']);
}


if (empty($_POST['classLevel'])) {
    $classLevelErr = 'Class level is required';
    $valid = false;
} else {
    $classLevel = test_input($_POST['classLevel']);
}


if (empty($_POST['essayGrade'])) {

  $essayGradeErr = 'Essay Grade is required';
  $valid = false;
}
else {
  $essayGrade = $_POST['essayGrade'];
  $essayGrade = test_input($essayGrade); 

  if (!preg_match('/^[a-zA-Z]$/', $essayGrade)) {     
      $essayGradeErr = 'Essay Grade is only 1 character in length';
      $valid = false;
  }
}

   if (empty($_POST['essayDate'])) {

      $essayDateErr = 'Essay Date is required';
      $valid = false;
  }
  else {
      $essayDate = $_POST['essayDate'];
      $essayDate = test_input($essayDate); 

      if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $essayDate)) {     
          $essayDateErr = 'Essay date must be a valid date';
          $valid = false;
      }
  }
  

if (empty($_POST['fullEssay'])) {

  $fullEssayErr = 'Full Essay is required';
  $valid = false;
}
else {
  $fullEssay = $_POST['fullEssay'];
  $fullEssay = test_input($fullEssay); 

  if (!preg_match('/^[a-zA-Z0-9\W]{200,}$/', $fullEssay)) {     
      $fullEssayErr = 'Essay must be at least 200 characters or more in length';
      $valid = false;
  }
}
// ============================================================================================================================

    $essayListQry = "insert into essaylist (isPublic, essayID, username, studentName, essayTitle, essayFirstParagraph, essayRating) values ('$isPublic', '$essayID', '$username', '$studentName', '$essayTitle', '$essayFirstParagraph', '$essayRating')";

    $essayDetailsQry = "insert into essaydetails (essayID, username, essayTitle, fullEssay, essayDate, studentName, schoolName, classLevel) values ('$essayID','$username', '$essayTitle', '$fullEssay', '$essayDate','$studentName', '$schoolName', '$classLevel')";

    $result1 = null;
    $result2 = null;

    
    try {
      $result1 = mysqli_query($conn, $essayListQry);
      
      if ($result1) {
          $result2 = mysqli_query($conn, $essayDetailsQry);
      }
      
      if($result1 && $result2) {
          echo '<br><br>Your essay has been added successfully. Thank You.<br><br>';
      } elseif ($result1) {
          echo '<br><br>Your essay has been added successfully. Thank You.<br><br>';
      } else {
          echo '<br><br>Failed to add your essay. Please try again.<br><br>';
      }
      
  } catch(Exception $e) {
      echo '<br><br>Error occurred: ' . mysqli_error($conn) . '<br><br>';
      echo "Please <a href=\"index.html\">return to form</a> to resubmit";
  }
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

    <div class="essayUpload-form">
        <h2 class="form-heading">Essay Upload</h2>
        <h4 class="Upload-instruction">It's simple and easy</h4>
        <img class="reg-logo" src="../IMGS/LOGO/Logo.png" alt="">

        <form class = "account-info essay-info" method = "POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" onsubmit="return validate()">
         <input type="hidden" id="schoolName" name="schoolName" type="text" placeholder="SchoolName" value="<?php echo $_SESSION['schoolName'];?>"/><br>
        
        <input type="hidden" id="classLevel" name="classLevel" type="text" placeholder="classLevel" value="<?php echo $_SESSION['classLevel'];?>"/><br>

          <input type="hidden" id="username" name="username" type="text" placeholder="username" value="<?php echo $_SESSION['username'];?>"/><br>
          <span id="usernameErr" class="error"><?php echo $usernameErr; ?></span>

          <input type="hidden" id="studentName" name="studentName" type="text" placeholder="Student Full Name" value="<?php echo $_SESSION['fullName'];?>"/><br>
          <span id="studentNameErr" class="error"><?php echo $studentNameErr; ?></span> 

        <Label for>Do you want your essay to be public</Label>
        <div class="isPublic">
        Yes <input type="radio" name="isPublic" value= 1>
        No <input type="radio" name="isPublic" value= 0>
        </div>  

        <input  class="essay-input" id="essayID" name="essayID" type="text" placeholder="Essay ID" value="<?php echo $essayID;?>"/><br>
        <span id="essayIDErr" class="error"><?php echo $essayIDErr; ?></span> 
        

        <input  class="essay-input" id="essayTitle" name="essayTitle" type="text" placeholder="Essay Title" value="<?php echo $essayTitle;?>"/><br>
        <span id="essayTitleErr" class="error"><?php echo $essayTitleErr; ?></span> 


        <input class="essay-input" id="essayDate" name = "essayDate" type="text" placeholder ="Essay Date" onfocus="(this.type = 'date')"value="<?php echo $essayDate; ?>"/>
        <span id="essayDateErr" class="error"><?php echo $essayDateErr; ?></span>


           
        <textarea class="essay-input" id = "essayFirstParagraph" name="essayFirstParagraph" rows="6" cols="70" placeholder="Please enter, ONLY the first paragraph of your essay here" value="<?php echo $essayFirstParagraph; ?>"/></textarea><br>
        <span id="essayFirstParagraphErr" class="error"><?php echo $essayFirstParagraphErr; ?></span>


        <textarea class="essay-input" id = "fullEssay" name="fullEssay" rows="20" cols="255" placeholder="Please enter the complete essay here" value="<?php echo $fullEssay; ?>"/></textarea><br>
        <span id="fullEssayErr" class="error"><?php echo $fullEssayErr; ?></span>

        <input type="submit" name="submit" class="submit-btn">
        </form>



      </div>
      <script type="text/javascript" src="../JS/essayUploadValidation.js"></script>

</body>
</html>