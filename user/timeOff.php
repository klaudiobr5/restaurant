<?php
session_start();
?>
<html>
<head>
  <title>TimeOff</title>
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
  if(isset($_POST['addOff'])) {
    $start=$_POST['start'];
    $end=$_POST['end'];
    
    if ($start>$end){
        echo '<script>alert("Start date must be kess than end date")</script>';
    }
    else{
        include_once('../conn/db.php'); //gia sindesi me ti basi dedomenon
        $sql = "insert into timeOff (Username, OffDateStart, OffDateEnd) values (:username, :start, :end)";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':username', $_SESSION["username"], PDO::PARAM_STR);
        $statement->bindParam(':start', $start, PDO::PARAM_STR);
        $statement->bindParam(':end', $end, PDO::PARAM_STR);
        $value = $statement->execute();
        if ($value){
            echo '<script>alert("Time Off added.")</script>';
        }
        else{
            echo '<script>alert("Addition error!")</script>';
        }

        
    }
  }
  include_once('../conn/db.php');
  if ($_SESSION["type"]==1) { //is admin
    ?>
    <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
    <div class="container">
        <h1 class="display-4">Approve</h1>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
        <tr>
        <th>Employee</th><th>Start Date</th><th>End Date</th><th>Approve</th>
        </tr>
        <tbody>
            <?php
                $sql = "select * from timeoff inner join users on users.Username = timeoff.Username where approve=0 order by OffDateStart, OffDateEnd";
                $statement = $pdo->prepare($sql);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                    echo "<tr><td>".$row['Fullname']."</td>";
                    echo "<td>".$row['OffDateStart']."</td>";
                    echo "<td>".$row['OffDateEnd']."</td>";
                    echo "<td><form method='post'><input type='hidden' name='id' value='".$row['timeOffID']."'>";
                    echo "<input type='submit' class='btn btn-primary' value='Approve' name='approve'></form></td>";
                    
                    echo "</tr>";
                }
            ?>
        </tbody>
		</table>
    </div>
<?php
  }
  else if ($_SESSION["type"]>2){
?>
  <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
    <div class="container">
        <h1 class="display-4">Add Time Off</h1>
        </div>
    </div>

    <form method="post" action="">
    <div class="d-flex justify-content-center">
        <div class="form-group">
            <label for="start">Start date:</label>
            <input type="date" id="start" class="form-control" name="start"></div>
        <div class="form-group">
            <label for="end">End date:</label>
            <input type="date" id="end" class="form-control" name="end" ></div>
        
        <div class="form-group">
            <input type="submit" value="Add" class="btn btn-primary btn-lg btn-block" name="addOff"></div>
    </form> 
</div>
  </div>
  <?php
  }
  ?>
</body>
</html>