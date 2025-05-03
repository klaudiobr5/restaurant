<?php
session_start();
?>
<html>
<head>
  <title>Add employee</title>
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
  include_once('menuUser.php');
  if(isset($_POST['addEmpl'])) {
    $fullname=$_POST['fullname'];
    $email=$_POST['email'];
    $address=$_POST['address'];
    $username=$_POST['username'];
    $pass=$_POST['pass'];
    $type=$_POST['type'];
    if (strlen($fullname)<4 || strlen($email)<6 || strlen($address)<6 || strlen($username)<4 || strlen($pass)<4){
        echo '<script>alert("Please fill all fields!")</script>';
    }
    else{
        include_once('../conn/db.php'); //gia sindesi me ti basi dedomenon
        $sql = "SELECT * FROM users where username=:username";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();
        if ( $statement->rowCount() == 0){
            $sql = "insert into users (Username, password, fullname, address, email, StaffTypeId) values(";
            $sql .= ":username, :pass, :fullname, :address, :email, :type)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':username', $username, PDO::PARAM_STR);
            $statement->bindParam(':pass', $pass, PDO::PARAM_STR);
            $statement->bindParam(':fullname', $fullname, PDO::PARAM_STR);
            $statement->bindParam(':address', $address, PDO::PARAM_STR);
            $statement->bindParam(':email', $email, PDO::PARAM_STR);
            $statement->bindParam(':type', $type, PDO::PARAM_INT);
            $value = $statement->execute();
            if ($value){
                echo '<script>alert("Employee added.")</script>';
            }
            else{
                echo '<script>alert("Addition error!")</script>';
            }
        
        }
        else{
            echo '<script>alert("Username already exist!")</script>';
        }
    }
  }
?>
  <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
    <div class="container">
        <h1 class="display-4">Add Employee</h1>
        </div>
    </div>

    <form method="post" action="">
        <div class="form-group">
            <label for="fullname">Full name:</label>
            <input type="text" id="fullname" class="form-control" name="fullname" value="" ></div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" class="form-control" name="address" value="" ></div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="text" id="email" class="form-control" name="email" value="" ></div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" class="form-control" name="username" value="" ></div>
        <div class="form-group">
            <label for="pass">Password:</label>
            <input type="password" id="pass" class="form-control" name="pass" value="" ></div>
        <div class="form-group">
            <label for="type">Staff Type:</label>
            <select id="type" class="form-control" name="type">
                <option value="3">Wait</option>
                <option value="4">Kitchen</option>
                <option value="5">Inventory</option>
            </select>
            </div>
        <div class="form-group">
            <input type="submit" value="Add" class="btn btn-primary btn-lg btn-block" name="addEmpl"></div>
    </form> 
</div>
</body>
</html>