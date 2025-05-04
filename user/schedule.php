<?php
session_start();
?>
<html>
<head>
  <title>Schedule</title>
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
  include_once('../conn/db.php');
  include_once('functions.php');
  if ($_SESSION["type"]==1) { // admin can add new schedules
    ?>
    <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
    <div class="container">
        <h1 class="display-4">Schedules</h1>
        </div>
    </div>
  <!-- Show form to select date for schedules -->
    <form method="post" action="">
    <div class="d-flex justify-content-center">
        <div class="form-group">
            <label for="start">Show schedule for:</label>
            <input type="date" id="start" class="form-control" name="start" value="<?php echo date('Y-m-d'); ?>"></div>
        </div>
        <div class="form-group">
            <input type="submit" value="Show schedule" class="btn btn-primary btn-lg btn-block" name="showSched"></div>
    </form>
  <!-- End form -->
    <?php 
    if(isset($_POST['showSched']) || isset($_POST['toadd']) || isset($_POST['delete'])) { /*admin selected a day to show schedule
        or deleted/added a schedule */
       if (isset($_POST['toadd'])) { //if added a schedule
            if ($_POST['startTime']<"18:00" || $_POST['endTime']<"18:00"){ //check time to be between 18-24 
                echo '<script>alert("Time must be between 18 and 24!")</script>';
            }
            else {
                if ($_POST['startTime']<$_POST['endTime']){ //check start time < end time
                    //insert to database
                    $sql = "insert into schedules (Username, ScheduleDate, ScheduleStart, ScheduleEnd) values (:uname, :sd, :ss, :se)"; //insert schedule
                    $statement = $pdo->prepare($sql);
                    $statement->bindParam(':uname', $_POST['id'], PDO::PARAM_STR);
                    $statement->bindParam(':sd', $_POST['resDate'], PDO::PARAM_STR);
                    $statement->bindParam(':ss', $_POST['startTime'], PDO::PARAM_STR);
                    $statement->bindParam(':se', $_POST['endTime'], PDO::PARAM_STR);
                    $value = $statement->execute();
                    if ($value){
                        echo '<script>alert("Added Schedule.")</script>';
                    }
                    else{
                        echo '<script>alert("Add error!")</script>';
                    } 
                }
                else{
                    echo '<script>alert("Start time must be less than End Time.")</script>';
                }
        }
        }
        if (isset($_POST['delete'])) { //selected to delete a schedule
            $sql = "delete from schedules where Username=:uname and ScheduleDate=:sd"; 
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':uname', $_POST['id'], PDO::PARAM_STR);
            $statement->bindParam(':sd', $_POST['resDate'], PDO::PARAM_STR);
            $value = $statement->execute();
            if ($value){
                echo '<script>alert("Schedule Deleted.")</script>';
            }
            else{
                echo '<script>alert("Delete error!")</script>';
            } 
        }
        ?>
        <?php
         addSchedule($pdo); //call function to show tables first with the schedules and second with available employees
        }
        ?>
        
    </div>
    </div>
   

<?php
  }
  else if ($_SESSION["type"]>2){ //if the user is not admin or customer 
?>
  <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
    <div class="container">
        <h1 class="display-4">Show schedule</h1>
        </div>
    </div>

    
    <table class="table table-bordered">
        <thead>
        <tr><th colspan="3">Approved</th></tr>
        <tr>
        <th>Date</th><th>Start Hour</th><th>End Hour</th>
        </tr>
        <tbody>
            <?php
                //get all schedules
                $sql = "select * from schedules where Username=:username and ScheduleDate>=CURDATE() order by ScheduleDate";
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':username', $_SESSION["username"], PDO::PARAM_STR);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                    echo "<tr>";
                    echo "<td>".$row['ScheduleDate']."</td>";
                    echo "<td>".$row['ScheduleStart']."</td>";
                    echo "<td>".$row['ScheduleEnd']."</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
		</table>
        <table class="table table-bordered">
        <thead>
        <tr><th colspan="3">Approved</th></tr>
        <tr>
        <th>Year</th><th>Month</th><th>Total Hours</th>
        </tr>
        <tbody>
            <?php
                //get all schedules group by year, month and sum time worked
                $sql = "select year(ScheduleDate) as ye, month(ScheduleDate) as mo, SEC_TO_TIME(sum(TIME_TO_SEC(TotalHours))) as su from schedules where Username=:username group by year(ScheduleDate), month(ScheduleDate)";
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':username', $_SESSION["username"], PDO::PARAM_STR);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                    echo "<tr>";
                    echo "<td>".$row['ye']."</td>";
                    echo "<td>".$row['mo']."</td>";
                    $h = substr($row['su'], 0, strpos($row['su'], ":"));
                    echo "<td>".$row['su']."</td>";
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