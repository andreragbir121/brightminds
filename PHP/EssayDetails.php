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
    <title>Essay Details</title>
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
                <img src="<?php echo !empty($_SESSION['pfp']) ? $_SESSION['pfp'] : 'IMGS/Profile-pictures/avatar1.png'; ?>" alt="Profile">
            </div>
            <div class="profile-selection">
                <a class="profile-options">Profile</a>
                <a class="profile-options" href="">Preference</a>
                <?php if (isset($_SESSION['username'])) { 
                    echo '<a class="profile-options" href="?logout=1">Logout</a>';
                } else { 
                    echo '<a class="profile-options" href="#login">Login</a>';
                } ?>
                </div>
            </div>
    </nav>
    <?php

$essayID = 'essayID';
if (isset($_GET['essayID'])) {
    $essayID = $_GET['essayID'];
}
$query = "select * from essaydetails where essayID = '$essayID'";

$result = null; 

try { 
  $result = mysqli_query($conn, $query);
} catch (Exception $e){
  echo '<br><br>Error occurred: ' . mysqli_error($conn) . '<br><br>';
} 
// 4. process the result and give user feedback
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "
            <div class='essay-details-container'>
                <div class='student-info'>
                    <div class='student-details'>
                        <p class='student-detail'>Username: <span>{$row['username']}</span></p>
                        <p class='student-detail'>Student Name: <span>{$row['studentName']}</span></p>
                        <p class='student-detail'>School: <span>{$row['schoolName']}</span></p>
                        <p class='student-detail'>Class: <span>{$row['classLevel']}</span></p>
                        <p class='student-detail'>Date: <span>{$row['essayDate']}</span></p>                        
                    </div>
                </div>
                
                <div class='essay-content'>
                    <h1 class='essay-title'>{$row['essayTitle']}</h1>
                    <p class='student-detail'>{$row['fullEssay']}</p>
                </div>
            </div>";
            ?>
            <br><br><br>
            <p class="reference">Regerence: View full essay here:<br>  
            Tapas. (2022, October 26). Best 20 Short Essay Writing Examples - English Luv. English Luv. <br>
            <a href="https://englishluv.com/short-essay-writing/">https://englishluv.com/short-essay-writing/</a></p>

            <br><br><br>

            <?php echo
             "<div class='instructor-info'>
                    <div class='instructor-details'>
                        <p class='instructor-detail'>Instructor Name: <span>{$row['instructorName']}</span></p>
                        <p class='instructor-detail'>Essay Rating: <span>{$row['essayRating']}</span></p>
                        <p class='instructor-detail'>Grade: <span>{$row['grade']}</span></p>
                        <p class='instructor-detail'>Comment: <span>{$row['comment']}</span></p>
            </div>
                    </div>";
        }
    } else {
        echo "<div class='no-results'>No essays found for this user.</div>";
    }
} else {
    echo "<div class='error'>Error retrieving essay: ".mysqli_error($conn)."</div>";
}

$instructorID = $instructorName = $essayRating = $grade = $comment = "";
$instructorIDErr = $instructorNameErr = $essayRatingErr = $gradeErr = $commentErr = "";

$valid = true;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['submit'])) {
    if (isset($_POST['essayID'])) $essayID = $_POST['essayID']; 

  if (empty($_POST['instructorID'])) {

    $instructorIDErr = 'Instructor ID is needed';
    $valid = false;
}
else {
      if (isset($_POST['instructorID'])) $instructorID = $_POST['instructorID']; 
    $instructorID = test_input($instructorID);

    if (!preg_match("/^\d+$/", $instructorID)) {
        $instructorIDErr = 'Instructor ID must be a valid number';
        $valid = false;
    }
}
 
                   
  if (empty($_POST['instructorName'])) {

    $instructorNameErr = 'Instructor Name is required';
    $valid = false;
}
else {
      if (isset($_POST['instructorName'])) $instructorName = $_POST['instructorName']; 
    $instructorName = test_input($instructorName);

    if (!preg_match("/[a-zA-Z]+[ a-zA-Z]*/", $instructorName)) {
        $instructorNameErr = 'instructor name must be only letters and spacing';
        $valid = false;
    }
}



if (empty($_POST["essayRating"])) {
    $essayRatingErr = "Essay rating is required ";
    $valid = false;
} else {
    if (isset($_POST['essayRating'])) $essayRating = $_POST['essayRating']; 
    $essayRatingErr = test_input($_POST["essayRating"]);
}




if (empty($_POST["grade"])) {
    $gradeErr = "Grade is required ";
    $valid = false;
} else {
    if (isset($_POST['grade'])) $grade = $_POST['grade']; 
    $grade = test_input($_POST["grade"]);
}

                   
  if (empty($_POST["comment"])) {

    $commentErr = "comment is needed for student feedback";
    $valid = false;
}
else {
      if (isset($_POST['comment'])) $comment = $_POST['comment'];  
    $comment = test_input($comment);

    if (!preg_match("/^.{5,}$/", $comment)) {
        $commentErr = "Comment must be at least 5 characters minimum";
        $valid = false;
    }
}

    $query = "update essaylist set essayRating = '$essayRating', isPublic = 1, grade = '$grade' where essayID = '$essayID'";

    $query1 = "update essaydetails set essayRating = '$essayRating', instructorID = '$instructorID', instructorName = '$instructorName', grade = '$grade', comment = '$comment' where essayID = '$essayID'";
    
    //execute the query  
    $result1 = null;    
    $result2 = null;

    
    try {
      $result1 = mysqli_query($conn, $query);
      
      if ($result1) {
          $result2 = mysqli_query($conn, $query1);
      }
      
      if($result1 && $result2) {
          echo '<br><br>Your feedback has been added. Thank You.<br><br>';
      } elseif ($result1) {
          echo '<br><br>essay Rating has been added to the essay list but failed to insert comment and grade into essay details.<br><br>';
      } else {
          echo '<br><br>Failed to add feedback to the essay. Please try again.<br><br>';
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

if (isset($_SESSION['userType']) && $_SESSION['userType'] === 'instructor') {
?>

 <div class="feedback-form">
        <h2 class="feedback-heading">Feedback</h2>
        <h4 class="feedback-instruction">Leave your feedback to the student</h4>

        <form class = "feedback-info" method = "POST" action="<?php echo ($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" onsubmit="return validate()">

            <select class="feedback-input" id="essayRating" name="essayRating">
                        <option value="">Essay Rating</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
            </select>
            <span id="gradeErr" class="error"><?php echo $essayRatingErr; ?></span>

            <select class="feedback-input" id="grade" name="grade">
                        <option value="">Essay Grade</option>
                        <option value="A (Excellent)">A (Excellent)</option>
                        <option value="B (Good)">B (Good)</option>
                        <option value="C (Satisfactory)">C (Satisfactory)</option>
                        <option value="D (Needs Improvement)">D (Needs Improvement)</option>
                        <option value="F (Fail)">F (Fail)</option>
            </select>
            <span id="gradeErr" class="error"><?php echo $gradeErr; ?></span>

            
          <textarea class="feedback-input" id = "comment" name="comment" rows="3" cols="255" placeholder="instructor comment to the student" value="<?php echo $comment; ?>"/></textarea><br>
          <span id="commentErr" class="error"><?php echo $commentErr; ?></span>

        <input type = "hidden" name="essayID" value="<?php echo ($essayID); ?>">

        <input type = "hidden" id="instructorID" name="instructorID" type="num" placeholder="instructorID" value="<?php echo $_SESSION['instructorID'];?>"/><br>
        <span id="instructorIDErr" class="error"><?php echo $instructorIDErr; ?></span>

        <input type = "hidden" id="instructorName" name="instructorName" type="text" placeholder="Full Name" value="<?php echo $_SESSION['fullName'];?>"/><br>
        <span id="instructorNameErr" class="error"><?php echo $instructorNameErr; ?></span>
          <input type="submit" name="submit" class="submit-btn">
        </form>

      </div>
      <?php } 
      
      mysqli_close($conn);
      
      ?>
      <script type="text/javascript" src="../JS/instructorValidation.js"></script>

</body>
</html>

