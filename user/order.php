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
  if ($_SESSION["type"]==3) { //Only Wait staff can add new order
    ?>
    <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
    <div class="container">
        <h1 class="display-4">Order</h1>
        </div>
    </div>
  <!-- Show form to select Table -->
    <form method="post" action="">
    <div class="d-flex justify-content-center">
        <div class="form-group">
            <label for="start">Table Number:</label>
            <select class="form-select" name="tableNumber">
            <?php
            //show tables that are booked for current day
            $sql = "select tables.TableNumber as tn from tables inner join booking on tables.TableNumber=booking.TableNumber where BookDate=curdate() order by tables.TableNumber";
            $statement = $pdo->prepare($sql);
            $result = $statement->execute();
            while ($row = $statement->fetch()) {
                echo "<option value='".$row['tn']."'>".$row['tn']."</option>";
            }
            ?>
            </select></div>
        </div>
        <div class="form-group">
          <?php
            if ($statement->rowCount()>0){ ?>
            <input type="submit" value="Start Order" class="btn btn-primary btn-lg btn-block" name="startOrder"></div>
            <?php
            }
            else{ ?>
            <input type="submit" disabled value="Start Order" class="btn btn-primary btn-lg btn-block" name="startOrder"></div>
            <?php
            } ?>
    </form>
  <!-- End form -->
    <?php 
    if(isset($_POST['startOrder']) || isset($_POST['addItem']) || isset($_POST['finishOrder']) || isset($_POST['deleteItem'])) { 
      /*wait staff selected table  
      or add item or finish order or delete Item*/
       if (isset($_POST['startOrder']) || isset($_POST['addItem']) || isset($_POST['deleteItem'])){
        ?>
        <!-- Show form to select items -->
          <form method="post" action="">
              <?php $tableNumber = $_POST['tableNumber'];  
              //execute select to find the reservation id for the table 
              $sql = "select ReservationId from booking where BookDate=curdate() and TableNumber=:tn";
              $statement = $pdo->prepare($sql);
              $statement->bindParam(':tn', $tableNumber, PDO::PARAM_INT);
              $result = $statement->execute();
              $row = $statement->fetch(PDO::FETCH_ASSOC);
              $rId = $row['ReservationId'];
              if (!isset($_POST['addItem']) && !isset($_POST['deleteItem'])){ //if is a new order
                //execute insert to orders
                $sql = "insert into orders (ReservationID, Status, Username) values (:rId, -1, :uname)";
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':uname', $_SESSION['username'], PDO::PARAM_STR);
                $statement->bindParam(':rId', $rId, PDO::PARAM_INT);
                $result = $statement->execute();
                $lastId = $pdo->lastInsertId();//get last auto increment id (order Id)
              }
              else {
                $lastId = $_POST['lastId']; //this is not a new order, so get order Id from post 
              }
              ?>
              <!-- form to select items to add to order -->
              <div class="form-group">
                  <label for="itemId">Item:</label>
                  <select class="form-select" name="itemId">
                  <?php
                  //get all items
                  $sql = "select * from items where QuantityOnHand>0 order by ItemName";
                  $statement = $pdo->prepare($sql);
                  $result = $statement->execute();
                  while ($row = $statement->fetch()) {
                      echo "<option value='".$row['ItemID']."'>".$row['ItemName']."</option>";
                  }
                  ?>
                  </select></div>
                  <div class="form-group">
                  <label for="quantity">Quantity:</label>
                  <input type="number" value="1" min="1" name="quantity" class="form-control" max="<?php echo $row['QuantityOnHand'];?>"></div>
                  <div class="form-group">
                  <label for="req">Special Requests:</label>
                  <input type="text" name="req" class="form-control"></div>
                  <input type="hidden" name="tableNumber" value="<?php echo $tableNumber; ?>">
                  <input type="hidden" name="lastId" value="<?php echo $lastId; ?>">
              <div class="form-group">
                  <input type="submit" value="Add Item" class="btn btn-info btn-lg btn-block" name="addItem"></div>
              <div class="form-group">
                  <input type="submit" value="FinishOrder" class="btn btn-success btn-lg btn-block" name="finishOrder"></div>
          </form>
        <!-- End form -->
        <?php
       }
       if (isset($_POST['finishOrder'])) { //if customer finish order 
            $itemId = $_POST['itemId'];//get all post values
            $quantity = $_POST['quantity'];
            $req = $_POST['req'];
            $tableNumber = $_POST['tableNumber'];
            $lastId = $_POST['lastId'];
            //update status of order from -1 to 0 (send to kitchen)
            $sql = "update orders set Status=0 where OrderId=:oid"; 
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':oid', $lastId, PDO::PARAM_INT);
            $value = $statement->execute();
            //redure inventory
            $sql = "select * from orderdetails where OrderID=:oid"; 
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':oid', $lastId, PDO::PARAM_INT);
            $result = $statement->execute();
            while ($row = $statement->fetch()) { //for every item
              $sql1 = "update items set QuantityOnHand=QuantityOnHand - :q where ItemId=:iid";
              $statement1 = $pdo->prepare($sql1);
              $statement1->bindParam(':q', $row['Quantity'], PDO::PARAM_INT);
              $statement1->bindParam(':iid', $row['ItemID'], PDO::PARAM_INT);
              $value = $statement1->execute();
            }
            if ($value){
                echo '<script>alert("Order sent to kitchen.")</script>';
            }
            else{
                echo '<script>alert("Order Problem!")</script>';
            } 
        }
        if (isset($_POST['addItem'])) { //if wait want to add an item to order
          $itemId = $_POST['itemId'];//get all post values
          $quantity = $_POST['quantity'];
          $req = $_POST['req'];
          $tableNumber = $_POST['tableNumber'];
          $lastId = $_POST['lastId'];
          $sql = "insert into orderdetails (OrderID, ItemID, Quantity, 	SpecialRequests) values (:oid, :iid, :q, :sr)"; 
          $statement = $pdo->prepare($sql);
          $statement->bindParam(':oid', $lastId, PDO::PARAM_INT);
          $statement->bindParam(':iid', $itemId, PDO::PARAM_INT);
          $statement->bindParam(':q', $quantity, PDO::PARAM_INT);
          $statement->bindParam(':sr', $req, PDO::PARAM_STR);
          $value = $statement->execute();
          if ($value){
              echo '<script>alert("Item added to order.")</script>';
          }
          else{
              echo '<script>alert("Addition problem!")</script>';
          } 

        }
        if (isset($_POST['deleteItem'])) { //if customer select to delete an item from order 
          $iid = $_POST['iid'];//get all post values
          $lastId = $_POST['lastId'];
          //delete it from table
          $sql = "delete from orderdetails where OrderID=:oid and ItemID=:iid"; 
          $statement = $pdo->prepare($sql);
          $statement->bindParam(':oid', $lastId, PDO::PARAM_INT);
          $statement->bindParam(':iid', $iid, PDO::PARAM_INT);
          $value = $statement->execute();
          if ($value){
              echo '<script>alert("Deleted Item.")</script>';
          }
          else{
              echo '<script>alert("Deletion Problem!")</script>';
          } 
      }
        ?>
        <?php
        if (isset($_POST['addItem']) || isset($_POST['deleteItem'])){ 
         ?>
         <table class="table table-bordered">
            <thead>
            <tr><th colspan="3">Order Details</th></tr>
            <tr>
            <th>Item</th><th>Quantity</th><th>Special Request</th><th>Delete</th>
            </tr>
            <tbody>
            <?php
            $lastId = $_POST['lastId'];
            $sql = "select orderdetails.ItemID as iid, Quantity, SpecialRequests, ItemName from orderdetails inner join items on ";
            $sql .= " orderdetails.ItemID=items.ItemID where OrderId=:oid ";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':oid', $lastId, PDO::PARAM_INT);
            $result = $statement->execute();
            while ($row = $statement->fetch()) {
                echo "<tr>";
                echo "<td>".$row['ItemName']."</td>";
                echo "<td>".$row['Quantity']."</td>";
                echo "<td>".$row['SpecialRequests']."</td>";
                echo "<form method='post'><td><input type='hidden' name='iid' value='".$row['iid']."'>";
                echo "<input type='hidden' name='lastId' value='".$lastId."'>";
                echo "<input type='hidden' name='tableNumber' value='".$tableNumber."'>";
                echo "<input type='submit' class='btn btn-primary' value='Delete' name='deleteItem'></td>";
                echo "</tr>";
            }
            ?>
            </tbody>
            </table>
        <?php
        }
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