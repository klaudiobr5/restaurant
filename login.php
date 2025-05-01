<?php
session_start();
?>
<?php
if(isset($_POST['submitLogin']) )   //Check if user has submitted the form
{

    $username = $_POST['username'];
    $password1 = $_POST['password'];
    include_once('conn/db.php'); //gia sindesi me ti basi dedomenon

    $sql = "SELECT * FROM users where username=:username and  password=:password";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':username', $username, PDO::PARAM_STR);
    $statement->bindParam(':password', $password1, PDO::PARAM_STR);
    $statement->execute();

    if ( $statement->rowCount() >0){
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $_SESSION["type"] = $result["StaffTypeID"];
        $_SESSION["username"] = $username;
        $_SESSION["onoma"] = $result["fullname"];
        Header("Location:user/centralPage.php");
    }
}

?>


<!DOCTYPE html>
<html>
<head>
  <title>Σύνδεση χρήστη</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<?php
include_once('menuVisitor.php');
?>
<div class="container">
<div class="jumbotron jumbotron-fluid text-center">
  <div class="container">
    <h1 class="display-4">Σύνδεση</h1>
</div>
</div>

<form method="POST">
  <div class="form-group">
     
      <input id="username" type="text" class="form-control" name="username" placeholder="Username">
    </div>
    <div class="form-group">
      
      <input id="password" type="password" class="form-control" name="password" placeholder="Password">
    </div>
  <div class="form-group">
       <button type="submit" class="btn btn-primary btn-lg btn-block" name="submitLogin">Login</button>
    </div>
</form>
</div>

</body>


<html>