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

<form class="box-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
  <div class="sillhouette"><i class="far fa-user"></i></div>
  <div class="box-field rect-round-sm">
    <div class="box-user-sill"><i class="fas fa-user"></i></div>
    <input type="text"      id="user" name="username" placeholder="USER ID"><br>
  </div>
  <div class="box-field  rect-round-sm">
    <div class="box-user-sill"><i class="fas fa-lock"></i></div>
    <input type="password"  id="pass" name="password" placeholder="PASSWORD"><br>
  </div>
  <input type="submit"                      value="LOGIN" class="rect-round-sm"><br>
  <input type="checkbox"    name="remember"> Remember Me
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
    
    $conn->close();
    
    if ($login == "none") {
      echo "Invalid User ID or Password";
      session_abort();
    }
    
    else {
      $_SESSION["userid"]   = $username;
          
      if ($login == "student") {
        $_SESSION["usertype"] = "student";
        header("Location: student-home.php");
      }
      
      elseif  ($login == "instructor") {
        $_SESSION["usertype"] = "instructor";
        header("Location: instructor-home.php");
      }
    }
  }
?>


<footer>
  <span>Designed by Department of Information Technology</span>
</footer>

</body>

</html>
