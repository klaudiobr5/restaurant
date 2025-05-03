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
  else if(isset($_POST['approve'])) {//when approve is pressed
    $id=$_POST['id'];
    include_once('../conn/db.php'); //gia sindesi me ti basi dedomenon
    $sql = "update timeOff set Approve=2 where timeOffID=:id";//update to value 2
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $value = $statement->execute();
    if ($value){
        echo '<script>alert("Approved.")</script>';
    }
    else{
        echo '<script>alert("Approve error!")</script>';
    }
  }
  else if(isset($_POST['reject'])) { //when reject is pressed
    $id=$_POST['id'];
    include_once('../conn/db.php'); //gia sindesi me ti basi dedomenon
    $sql = "update timeOff set Approve=1 where timeOffID=:id"; //update to value 1
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $value = $statement->execute();
    if ($value){
        echo '<script>alert("Rejected.")</script>';
    }
    else{
        echo '<script>alert("Reject error!")</script>';
    }
  }
  include_once('../conn/db.php');
  if ($_SESSION["type"]==1) { // admin can accept or reject 
    ?>
    <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
    <div class="container">
        <h1 class="display-4">Approve/Reject Requests</h1>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
        <tr><th colspan="5">Approve/Reject</th></tr>
        <tr>
        <th>Employee</th><th>Start Date</th><th>End Date</th><th>Approve</th><th>Reject</th>
        </tr>
        <tbody>
            <?php
                //get all requests that are not accepted/rejected (0:new request, 1:reject, 2:accept)
                $sql = "select * from timeoff inner join users on users.Username = timeoff.Username where approve=0 order by OffDateStart, OffDateEnd";
                $statement = $pdo->prepare($sql);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                    echo "<tr><td>".$row['Fullname']."</td>";
                    echo "<td>".$row['OffDateStart']."</td>";
                    echo "<td>".$row['OffDateEnd']."</td>";
                    /* show two buttons in a form and a hidden field with value of timeOffID. 
                    When admin press accept or reject call the same php file and based on the value from the 
                    hidden field, update properly the table  */
                    echo "<form method='post'><td><input type='hidden' name='id' value='".$row['timeOffID']."'>";
                    echo "<input type='submit' class='btn btn-primary' value='Approve' name='approve'></td>";
                    echo "<td><input type='submit' class='btn btn-primary' value='Reject' name='reject'></td></form>";
                    
                    echo "</tr>";
                }
            ?>
        </tbody>
		</table>
        <table class="table table-bordered">
        <thead>
        <tr><th colspan="3">Approved</th></tr>
        <tr>
        <th>Employee</th><th>Start Date</th><th>End Date</th>
        </tr>
        <tbody>
            <?php
                //get all requests that are not accepted/rejected (0:new request, 1:reject, 2:accept)
                $sql = "select * from timeoff inner join users on users.Username = timeoff.Username where approve=2 order by OffDateStart, OffDateEnd";
                $statement = $pdo->prepare($sql);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                    echo "<tr><td>".$row['Fullname']."</td>";
                    echo "<td>".$row['OffDateStart']."</td>";
                    echo "<td>".$row['OffDateEnd']."</td>";
                    
                    echo "</tr>";
                }
            ?>
        </tbody>
		</table>
    </div>
    </div>
   

<?php
  }
  else if ($_SESSION["type"]>2){ //if the user is not admin or customer 
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
        </div>
        <div class="form-group">
            <input type="submit" value="Add" class="btn btn-primary btn-lg btn-block" name="addOff"></div>
    </form> 
    <table class="table table-bordered">
        <thead>
        <tr><th colspan="4">Approved</th></tr>
        <tr>
        <th>Employee</th><th>Start Date</th><th>End Date</th><th>Accepted/Rejected</th>
        </tr>
        <tbody>
            <?php
                //get all requests that are not accepted/rejected (0:new request, 1:reject, 2:accept)
                $sql = "select * from timeoff inner join users on users.Username = timeoff.Username where users.Username=:username order by OffDateStart desc, OffDateEnd";
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':username', $_SESSION["username"], PDO::PARAM_STR);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                    echo "<tr><td>".$row['Fullname']."</td>";
                    echo "<td>".$row['OffDateStart']."</td>";
                    echo "<td>".$row['OffDateEnd']."</td>";
                    if ($row['Approve']==2){
                        echo "<td>Approved</td>";
                    }
                    if ($row['Approve']==1){
                        echo "<td>Rejected</td>";
                    }
                    else{
                        echo "<td>Pending</td>";
                    }
                    echo "</tr>";
                }
            ?>
        </tbody>
		</table>
</div>
  </div>
  <?php
  }
  ?>
</body>
</html>