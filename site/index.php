<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login</title>

  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">

  <link href="https://fonts.googleapis.com/css?family=Be+Vietnam:400,600,800&display=swap" rel="stylesheet">
</head>

<body>

<?php
  if (isset($_COOKIE["logout"]) && $_COOKIE["logout"] == "yes") {
    session_unset();
    session_destroy();
    setcookie("logout", "", time() - 3600);
  }

  if (isset($_COOKIE["loggedin"])) {
    if ($_COOKIE["usertype"] == "student")
      header("Location: student-home.php");

    else if ($_COOKIE["usertype"] == "instructor")
      header("Location: instructor-home.php");
  }
?>

<div class="page-container">

<div class="content-wrap">

<form class="box-form" method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>">
  <div class="sillhouette"><i class="far fa-user"></i></div>
  <div class="box-field rect-round-sm">
    <div class="box-user-sill"><i class="fas fa-user"></i></div>
    <input type="text"      id="user" name="username" placeholder="USER ID"><br>
  </div>
  <div class="box-field  rect-round-sm">
    <div class="box-user-sill"><i class="fas fa-lock"></i></div>
    <input type="password"  id="pass" name="password" placeholder="PASSWORD"><br>
  </div>
  <input type="submit"      value="LOGIN" class="rect-round-sm"><br>
  <input type="checkbox"    name="remember">Remember Me
  <span id="invalid-login"></span>
</form>

<?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $password = md5($password);
    
    $servname = "localhost";
    $conn = new mysqli($servname, "root", "password", "college_db");
    
    if ($conn->connect_error)
      die("Connection failed: " . $conn->connect_error);
    
    $sql = "SELECT StudentID, PassHash FROM STUDENT";
    $res = $conn->query($sql);
    
    $login = "none";
    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        if ($row['StudentID'] == $username && $password == $row['PassHash']) {
          $login = "student";
          break;
        }
      }
    }

    $sql = "SELECT InstructorID, PassHash FROM INSTRUCTOR";
    $res = $conn->query($sql);
    
    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        if ($row['InstructorID'] == $username && $password == $row['PassHash']) {
          $login = "instructor";
          break;
        }
      }
    }

    if ($username == '00000' && $password == md5("password"))
      $login = "admin";
    
    $conn->close();
    
    if ($login == "none") {
      echo
      '<script>
        document.getElementById("invalid-login").innerHTML = "Invalid User Id or Password";
      </script>';
      session_unset();
      session_abort();
    }
    
    else {
      echo
      '<script>
        document.getElementById("invalid-login").innerHTML = "";
      </script>';
      
      $_SESSION["userid"] = $username;

      if ($_REQUEST["remember"] == "on") {
        setcookie("loggedin", "yes");
      }
          
      if ($login == "admin") {
        $_SESSION["usertype"] = "admin";
        if ($_REQUEST["remember"] == "on")
          setcookie("usertype", "admin");

        header("Location: admin-home.php");
      }

      elseif ($login == "student") {
        $_SESSION["usertype"] = "student";
        if ($_REQUEST["remember"] == "on")
          setcookie("usertype", "student");

        header("Location: student-home.php");
      }
      
      elseif  ($login == "instructor") {
        $_SESSION["usertype"] = "instructor";
        if ($_REQUEST["remember"] == "on")
          setcookie("usertype", "instructor");

        header("Location: instructor-home.php");
      }
    }
  }
?>

</div>

</div>

</body>

</html>
