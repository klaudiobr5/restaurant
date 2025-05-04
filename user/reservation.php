<?php
session_start();
?>
<html>
<head>
  <title>Reservation</title>
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
  if ($_SESSION["type"]==2) { // customer can add new schedules
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
            <label for="start">New Reservation for:</label>
            <input type="date" id="start" class="form-control" name="start" value="<?php echo date('Y-m-d'); ?>"></div>
        </div>
        <div class="form-group">
            <input type="submit" value="Show schedule" class="btn btn-primary btn-lg btn-block" name="showRes"></div>
    </form>
  <!-- End form -->
    <?php 
    if(isset($_POST['showRes']) || isset($_POST['addReserve'])) { /*customer selected a day to show reservation
        or added a reservation */
       if (isset($_POST['addReserve'])) { //if added a schedule
            
            $res = $_POST['tables1'];
            //insert to database
            foreach ($res as $r){ 
                $sql = "insert into booking (Username, TableNumber, BookDate, BookTime, Guests) values (:uname, :t, :bd, :bt, :nog)"; //insert schedule
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':uname', $_SESSION['username'], PDO::PARAM_STR);
                $statement->bindParam(':t', $r, PDO::PARAM_INT);
                $statement->bindParam(':bd', $_POST['start'], PDO::PARAM_STR);
                $statement->bindParam(':bt', $_POST['startTime'], PDO::PARAM_STR);
                $statement->bindParam(':nog', $_POST['nog'], PDO::PARAM_INT);
                $value = $statement->execute();
                if ($value){
                    echo '<script>alert("Added booking.")</script>';
                }
                else{
                    echo '<script>alert("Booking error!")</script>';
                } 
            }
        
        }
        
        ?>
        <?php
         addReservation($pdo); //call function to show tables first with the schedules and second with available employees
        }
        ?>
        
    </div>
    </div>
   

<?php
  }
  else if ($_SESSION["type"]!=2){ //if the user is not customer 
?>
  <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
    <div class="container">
        <h1 class="display-4">Show schedule</h1>
        </div>
    </div>

    
            <table class="table table-bordered">
            <thead>
            <tr><th colspan="5">Reservations</th></tr>
            <tr>
            <th>Full name</th><th>Date</th><th>Hour</th><th>Table</th><th>Guests</th>
            </tr>
            <tbody>
            <?php
            //get all reservations and the full name of the user
            $sql = "select BookDate, BookTime,  TableNumber, Guests, FullName as fname from booking inner join users on booking.username=users.username where BookDate>=curdate() order by BookDate";
            $statement = $pdo->prepare($sql);
            $result = $statement->execute();
            while ($row = $statement->fetch()) {
                echo "<tr>";
                echo "<td>".$row['fname']."</td>";
                echo "<td>".$row['BookDate']."</td>";
                echo "<td>".$row['BookTime']."</td>";
                echo "<td>".$row['TableNumber']."</td>";
                echo "<td>".$row['Guests']."</td>";
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