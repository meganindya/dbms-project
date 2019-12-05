<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Courses</title>

  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">

  <link href="https://fonts.googleapis.com/css?family=Be+Vietnam:400,600,800&display=swap" rel="stylesheet">
</head>

<body>

<?php
  $servname = "localhost";
  $conn = new mysqli($servname, "root", "password", "college_db");

  if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);
?>

<div class="page-container">

<div class="user-label rect-circ">
  <span class="rect-circ">HEAD</span>
</div>

<div class="btn-logout rect-circ" onClick="logout()">
  <span class="rect-circ">LOGOUT</span>
  <div class="rect-circ">✖</div>
</div>

<div class="dept-label">
  <?php
    $sql = "SELECT DEPARTMENT.DeptName
            FROM INSTRUCTOR, DEPARTMENT
            WHERE INSTRUCTOR.InstructorID = ".$_SESSION["userid"].
            " AND INSTRUCTOR.DeptNo = DEPARTMENT.DeptNo";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        echo "<span>".$row['DeptName']."</span>";
      }
    }
  ?>
</div>

<form class="course-info-container" method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
  <input type="submit" value="SUBMIT" class="btn-submit rect-circ"/>

  <?php
    $courses = array();
    
    $sql = "SELECT COURSE.CourseID, COURSE.CourseName, COURSE.InstructorID FROM COURSE
            WHERE COURSE.DeptNo = ".$_SESSION["deptid"]." ORDER BY COURSE.CourseID";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        array_push($courses, 
          array($row['CourseID'], $row['CourseName'], $row['InstructorID']));
      }
    }

    for ($i = 0; $i < COUNT($courses); $i++) {
      $sql = "UPDATE COURSE SET InstructorID = '";
      $sql .= $_POST['insid'.$i]."', CourseName = '";
      $sql .= $_POST['crsnm'.$i]."', CourseID = '";
      $sql .= $_POST['crsid'.$i]."' WHERE CourseID = '".$courses[$i][0]."'";
      $res = $conn->query($sql);
    }

    $courses = array();

    $sql = "SELECT COURSE.CourseID, COURSE.CourseName, COURSE.InstructorID FROM COURSE
            WHERE COURSE.DeptNo = ".$_SESSION["deptid"]." ORDER BY COURSE.CourseID";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        array_push($courses,
          array($row['CourseID'], $row['CourseName'], $row['InstructorID']));
      }
    }

    $inslist = array();

    $sql = "SELECT * FROM INSTRUCTOR WHERE DeptNo = ".$_SESSION["deptid"];
    $res = $conn->query($sql);

    if($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        array_push($inslist, array($row['InstructorID'], $row['PersonID']));
      }
    }

    for ($i = 0; $i < COUNT($courses); $i++) {
      echo
      '<div class="course-info-course">
        <div class="rect-round-sm std-course-cred">
          <input value="'.$courses[$i][0].'" name="crsid'.$i.'"
            class="rect-round-sm std-course-id" maxlength=5/>

          <input value="'.$courses[$i][1].'" name="crsnm'.$i.'"
            class="rect-round-sm std-course-name" maxlength=31/>
        </div>

        <select class="rect-round-sm" name="insid'.$i.'" id="insid'.$i.'">';
          for ($j = 0; $j < COUNT($inslist); $j++) {
            if ($inslist[$j][0] == $courses[$i][2])
              echo '<option selected="selected" value="'.$inslist[$j][0].'">'.$inslist[$j][0].'</option>';
            
            else
              echo '<option value="'.$inslist[$j][0].'">'.$inslist[$j][0].'</option>';
          }
        echo '</select>
      </div>';
    }
  ?>
</form>

</div>

<script>
  function logout() {
    document.cookie = "courseid" + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    document.cookie = "loggedin" + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    document.cookie = "logout=yes";
    window.location.href = 'index.php';
  }
</script>

</body>

</html>
