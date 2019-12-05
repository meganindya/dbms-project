<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Administrator</title>

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

  $sql = "UPDATE PERSON SET FirstName = '";
  $sql .= $_POST['fname']."', MiddleName = '";
  $sql .= $_POST['mname']."', LastName = '";
  $sql .= $_POST['lname']."', Gender = ";
  if ($_POST['ugend'] == 'Male') $sql .= "'M' ";
  elseif ($_POST['ugend'] == 'Female') $sql .= "'F' ";
  elseif ($_POST['ugend'] == 'Transgender') $sql .= "'T' ";
  $sql .= "WHERE PersonID = '".$_SESSION["personid"]."'";
  $conn->query($sql);

  if ($_SESSION["phchanged"] == "true") {
    $sql = "DELETE FROM PHONE WHERE PersonID = '";
    $sql .= $_SESSION["personid"]."'";
    $conn->query($sql);

    for ($i = 1; $i <= 3; $i++) {
      if ($_POST["ph".$i] != "") {
        $sql = "INSERT INTO PHONE VALUES(";
        $sql .= $_SESSION["personid"].", ".$_POST["ph".$i].")";
        $conn->query($sql);
      }
    }

    $_SESSION["phchanged"] = "false";
  }


  if ($_SESSION["usertype"] == "instructor") {
    $sql = "SELECT DeptNo FROM DEPARTMENT WHERE DeptName = '";
    $sql .= $_POST['insdept']."'";
    $res = $conn->query($sql);

    $deptno = $res->fetch_assoc()['DeptNo'];

    $sql = "UPDATE INSTRUCTOR SET DeptNo = ".$deptno." WHERE InstructorID = '";
    $sql .= $_SESSION["userid"]."'";
    $conn->query($sql);
  }
?>

<div class="page-container">

<div class="content-wrap">

<div class="user-label rect-circ">
  <span class="rect-circ">ADMINISTRATOR</span>
</div>

<div class="btn-logout rect-circ" onClick="logout()">
  <span class="rect-circ">LOGOUT</span>
  <div class="rect-circ">✖</div>
</div>

<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="admin-user-edit">
  <input type="submit" value="SUBMIT" class="btn-submit rect-circ"/>

  <div class="user-type-label">
    <?php
      if ($_SESSION["usertype"] == "student")
        echo '<span>STUDENT</span>';
      elseif ($_SESSION["usertype"] == "instructor")
        echo '<span>INSTRUCTOR</span>';
    ?>
  </div>

  <?php
    $userarray = array();
    if ($_SESSION["usertype"] == "student") {
      $sql = "SELECT StudentID, FirstName, MiddleName, LastName, Gender, STUDENT.PersonID
              FROM STUDENT, PERSON
              WHERE STUDENT.PersonID = PERSON.PersonID
              AND StudentID = '";
      $sql .= $_SESSION["userid"]."'";
      $res = $conn->query($sql);

      if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
          $_SESSION["personid"] = $row['PersonID'];
          array_push($userarray,
            $row['StudentID'], $row['FirstName'], $row['MiddleName'], $row['LastName'], $row['Gender']);
        }
      }
    }

    elseif ($_SESSION["usertype"] == "instructor") {
      $sql = "SELECT InstructorID, FirstName, MiddleName, LastName, Gender, PERSON.PersonID
              FROM INSTRUCTOR, PERSON
              WHERE INSTRUCTOR.PersonID = PERSON.PersonID
              AND InstructorID = '";
      $sql .= $_SESSION["userid"]."'";
      $res = $conn->query($sql);

      if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
          $_SESSION["personid"] = $row['PersonID'];
          array_push($userarray,
            $row['InstructorID'], $row['FirstName'], $row['MiddleName'], $row['LastName'], $row['Gender']);
        }
      }
    }

    echo '<div class="user-person">
      <div class="rect-round-sm">'.$userarray[0].'</div>
      <input type="text" name="fname" value="'.$userarray[1].'" class="edit-id rect-round-sm">
      <input type="text" name="mname" value="'.$userarray[2].'" class="edit-id rect-round-sm">
      <input type="text" name="lname" value="'.$userarray[3].'" class="edit-id rect-round-sm">
      
      <select name="ugend" class="rect-round-sm">';
        if ($userarray[4] == 'M')
          echo '<option selected="selected">Male</option>';
        else
          echo '<option>Male</option>';
        
        if ($userarray[4] == 'F')
          echo '<option selected="selected">Female</option>';
        else
          echo '<option>Female</option>';

        if ($userarray[4] == 'T')
          echo '<option selected="selected">Transgender</option>';
        else
          echo '<option>Transgender</option>';
      echo '</select>
    </div>';
  ?>

  <?php
    $sql = "SELECT PhNo FROM PHONE WHERE PersonID = '";
    $sql .= $_SESSION["personid"]."'";
    $res = $conn->query($sql);

    $phones = array(); $pcnt = 0;
    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        array_push($phones, $row['PhNo']);
        $pcnt++;
        
        if ($pcnt == 3) break;
      }
    }

    echo
    '<div class="label-phone"><span>PHONES:</span></div>
    <input type="number" class="phno rect-round-sm" name="ph1" value="'.$phones[0].'" maxlength="10">
    <input type="number" class="phno rect-round-sm" name="ph2" value="'.$phones[1].'" maxlength="10">
    <input type="number" class="phno rect-round-sm" name="ph3" value="'.$phones[2].'" maxlength="10">';

    $_SESSION["phchanged"] = "true";


    if ($_SESSION["usertype"] == "instructor") {
      echo '<div class="label-dept"><span>DEPARTMENT:</span></div>
      <select class="user-dept-sel rect-round-sm" name="insdept">';

      $departments = array();
      $sql = "SELECT * FROM DEPARTMENT ORDER BY DeptNo";
      $res = $conn->query($sql);

      if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
          array_push($departments, array($row['DeptNo'], $row['DeptName']));
        }
      }

      $sql = "SELECT DeptNo FROM INSTRUCTOR WHERE InstructorID = '";
      $sql .= $_SESSION["userid"]."'";
      $res = $conn->query($sql);

      $deptno = 0;
      if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
          $deptno = $row['DeptNo'];
        }
      }

      for ($i = 0; $i < COUNT($departments); $i++) {
        if ($deptno == $departments[$i][0])
          echo '<option selected="selected" name="insdept" value="'.$departments[$i][1].'">'.$departments[$i][1].'</option>';
        else
          echo '<option name="insdept" value="'.$departments[$i][1].'">'.$departments[$i][1].'</option>';
      }
      echo '</select>';
    }
  ?>
</form>

</div>

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