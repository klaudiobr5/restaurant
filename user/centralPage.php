<?php
session_start();
if (!isset($_SESSION["type"])){
    Header("Location:../login.php");
  }
?>
<html>
<head>
  <title>Ads Site</title>
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
  include_once('../conn/db.php'); //gia sindesi me ti basi dedomenon
    if(isset($_POST['finishOrder'])) {//if kitchen staff finished preparation and pressed finish Order
        $oid=$_POST['oid'];
        $sql = "update orders set Status=1 where OrderID=:oid";//change status to 1 (ready to deliver by wait staff)
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':oid', $oid, PDO::PARAM_INT);
        $result = $statement->execute();
        
        if ($result){
            echo '<script>alert("Έτοιμη για παράδοση.")</script>';
        }
        else {
            echo '<script>alert("Πρόβλημα στην ενημέρωση της παραγγελίας.")</script>';
        }
        
    }
    else if(isset($_POST['deliverOrder'])) {//if wait staff delivered order
        $oid=$_POST['oid'];
        $sql = "update orders set Status=2 where OrderID=:oid";//change status to 2 (delivered)
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':oid', $oid, PDO::PARAM_INT);
        $result = $statement->execute();
        
        if ($result){
            echo '<script>alert("Delivered.")</script>';
        }
        else {
            echo '<script>alert("Problem in update order.")</script>';
        }
        
    }
?>
 <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
        <div class="container">
            <h1 class="display-4">

              <?php if ($_SESSION["type"] == "1") {
                ?>
                Welcome Manager
              <?php }
              else {
                ?>
                Welcome <?php echo $_SESSION["onoma"];?>
              <?php 
              }
              ?>
            </h1>
        </div>
    </div> 
    <?php
        include_once('../conn/db.php'); //gia sindesi me ti basi dedomenon
        if ($_SESSION["type"] == "2") { //if customer 
            ?>
            <table class="table table-bordered">
            <thead>
            <tr><th colspan="4">Reservations</th></tr>
            <tr>
            <th>Date</th><th>Hour</th><th>Table</th><th>Guests</th>
            </tr>
            <tbody>
            <?php
            $sql = "select * from booking where Username=:uname and BookDate>=curdate() order by BookDate";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':uname', $_SESSION['username'], PDO::PARAM_STR);
            $result = $statement->execute();
            while ($row = $statement->fetch()) {
                echo "<tr>";
                echo "<td>".$row['BookDate']."</td>";
                echo "<td>".$row['BookTime']."</td>";
                echo "<td>".$row['TableNumber']."</td>";
                echo "<td>".$row['Guests']."</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
            </table>
            <?php
        }
        else if ($_SESSION["type"] == "3") { //if wait staff show order ready to deliver
            ?>
            <table class="table table-bordered">
            <thead>
            <tr><th colspan="4">Orders ready to Deliver</th></tr>
            <tr>
            <th>Table</th><th>Order Id</th><th>Details</th><th>Deliver</th>
            </tr>
            <tbody>
            <?php
            $sql = "select * from orders inner join booking on orders.reservationID=booking.reservationID and BookDate=curdate() and orders.Status=1";
            $statement = $pdo->prepare($sql);
            $result = $statement->execute();
            while ($row = $statement->fetch()) {
                echo "<tr>";
                echo "<td>".$row['TableNumber']."</td>";
                echo "<td>".$row['OrderID']."</td>";
                $sql1 = "select * from orderdetails inner join items on orderdetails.itemID=items.itemID where orderId=:oid";
                $statement1 = $pdo->prepare($sql1);
                $statement1->bindParam(':oid', $row['OrderID'], PDO::PARAM_INT);
                $result1 = $statement1->execute();
                $orderDetails="";
                while ($row1 = $statement1->fetch()) {
                    $orderDetails .= $row1['ItemName']; 
                    $orderDetails .= " ".$row1['Quantity']."<br>";
                }
                echo "<td>".$orderDetails."</td>";
                echo "<form method='post'><td><input type='hidden' name='oid' value='".$row['OrderID']."'>";
                echo "<input type='submit' class='btn btn-primary' value='Deliver' name='deliverOrder'></td>";
                echo "</tr>";
            }
            ?>
            </tbody>
            </table>
            <?php
        }
        else if ($_SESSION["type"] == "4") { //if kitchen staff show order to be prepared by them
            ?>
            <table class="table table-bordered">
            <thead>
            <tr><th colspan="4">Orders To Prepare</th></tr>
            <tr>
            <th>Table</th><th>Order Id</th><th>Details</th><th>Finish</th>
            </tr>
            <tbody>
            <?php
            $sql = "select * from orders inner join booking on orders.reservationID=booking.reservationID and BookDate=curdate() and orders.Status=0";
            $statement = $pdo->prepare($sql);
            $result = $statement->execute();
            while ($row = $statement->fetch()) {
                echo "<tr>";
                echo "<td>".$row['TableNumber']."</td>";
                echo "<td>".$row['OrderID']."</td>";
                $sql1 = "select * from orderdetails inner join items on orderdetails.itemID=items.itemID where OrderID=:oid";
                $statement1 = $pdo->prepare($sql1);
                $statement1->bindParam(':oid', $row['OrderID'], PDO::PARAM_INT);
                $result1 = $statement1->execute();
                $orderDetails="";
                while ($row1 = $statement1->fetch()) {
                    $orderDetails .= $row1['ItemName']; 
                    $orderDetails .= " ".$row1['Quantity']."<br>";
                }
                echo "<td>".$orderDetails."</td>";
                echo "<form method='post'><td><input type='hidden' name='oid' value='".$row['OrderID']."'>";
                echo "<input type='submit' class='btn btn-primary' value='Finish' name='finishOrder'></td>";
                echo "</tr>";
            }
            ?>
            </tbody>
            </table>
            <?php
        }
        else if ($_SESSION["type"] == "1" || $_SESSION["type"] == "5") { //admin or inventory for stock alert
        ?>
            <table class="table table-bordered">
            <thead>
            <tr><th colspan="5">Stock Alerts</th></tr>
            <tr>
            <th>Item</th><th>Quantity on Hand</th><th>Reorder Level</th><th>Supplier name</th><th>Supplier email</th>
            </tr>
            <tbody>
            <?php
            $sql = "select * from items where QuantityOnHand<=ReorderLevel";
            $statement = $pdo->prepare($sql);
            $result = $statement->execute();
            while ($row = $statement->fetch()) {
                echo "<tr>";
                echo "<td>".$row['ItemName']."</td>";
                echo "<td>".$row['QuantityOnHand']."</td>";
                echo "<td>".$row['ReorderLevel']."</td>";
                echo "<td>".$row['SupplierName']."</td>";
                echo "<td>".$row['SupplierEmail']."</td>";
                echo "</tr>";
            }
        }
        ?>
</div> 
</body>
</html>