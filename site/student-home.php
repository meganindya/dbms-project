<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Student</title>

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

<div class="user-label rect-circ">
  <span class="rect-circ">STUDENT</span>
</div>

<div class="btn-logout rect-circ">
  <span class="rect-circ">LOGOUT</span>
  <div class="rect-circ">✖</div>
</div>

<div class="user-info">
  <?php
    $sql = "SELECT StudentID, PersonID FROM STUDENT
            WHERE STUDENT.StudentID = ".$_SESSION["userid"];
    $res = $conn->query($sql);

    $pid = -1;
    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        $pid = $row['PersonID'];
      }
    }

    $sql = "SELECT * FROM PERSON WHERE PERSON.PersonID = ".$pid;
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        $name = $row['FirstName']." ";
        if ($row['MiddleName'] != "")
          $name .= $row['MiddleName']." ";
        $name .= $row['LastName'];
      }
    }

    echo "<span>$name</span>";
    echo "<span>"; echo $_SESSION["userid"]; echo "</span>";
  ?>
</div>

<?php
  $courses = array();

  $sql = "SELECT * FROM UNDERTAKES, COURSE, DEPARTMENT
          WHERE UNDERTAKES.StudentID =".$_SESSION["userid"].
          " AND UNDERTAKES.CourseID = COURSE.CourseID
          AND DEPARTMENT.DeptNo = COURSE.DeptNo";
  $res = $conn->query($sql);
  
  if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
      array_push($courses,
        array($row['DeptName'], $row['CourseID'], $row['CourseName'],
          $row['ClassesTaken'], $row['Attendance'], $row['InternalMarks'], $row['PaperMarks']));
    }
  }


  for ($i = 0; $i < count($courses); $i++) {
    $total = $courses[$i][5] + $courses[$i][6];
    $grdpt = min(floor($total / 10) + 1, 10);

    echo
    '<div class="rect-round-sm std-course-container">
      <div class="std-course-info">
        <div class="rect-round-sm std-course-dept">
          <span>'.$courses[$i][0].'</span>
        </div>
      
        <div class="rect-round-sm std-course-cred">
          <div class="rect-round-sm std-course-id">
            <span>'.$courses[$i][1].'</span>
          </div>

          <div class="std-course-name">
            <span>'.$courses[$i][2].'</span>
          </div>
        </div>
      </div>

      <div class="std-course-data">
        <div class="std-course-attendance">
          <div class="std-course-attendance-data">
            <span>Total Classes: </span>
            <span>'.$courses[$i][3].'</span>
          </div>
  
          <div class="std-course-attendance-data">
            <span>Classes Attended: </span>
            <span>'.$courses[$i][4].'</span>
          </div>
  
          <div class="std-course-attendance-data">
            <span>Attendance: </span>
            <span>'.(intval($courses[$i][4]) / intval($courses[$i][3]) * 100).'%</span>
          </div>
        </div>
  
        <div class="std-course-marks">
          <div class="std-course-marks-data">
            <span>Internal Marks: </span>
            <span>'.$courses[$i][5].'</span>
          </div>
  
          <div class="std-course-marks-data">
            <span>Paper Marks: </span>
            <span>'.$courses[$i][6].'</span>
          </div>
  
          <div class="std-course-marks-data">
            <span>Total: </span>
            <span>'.$total.'</span>
          </div>
  
          <div class="std-course-marks-data">
            <span>Grade Point: </span>
            <span>'.$grdpt.'</span>
          </div>
        </div>
      </div>
    </div>';
  }
?>

<footer>
  <span>Designed by Department of Information Technology</span>
</footer>

</body>

</html>
