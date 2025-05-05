<?php
session_start();
if (!isset($_SESSION["type"])){
    Header("Location:../login.php");
  }
?>
<html>
<head>
  <title>Sales</title>
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
?>
 <div class="container">
    <div class="jumbotron jumbotron-fluid text-center">
        <div class="container">
            <h1 class="display-4">
                Sales
            </h1>
        </div>
    </div> 
    <table class="table table-bordered">
    <thead>
    <tr><th colspan="3">Total Quantity</th></tr>
    <tr>
    <th>Book Date</th><th>Item Name</th><th>Total Quantity</th>
    </tr>
    <tbody>
    <?php
    $sql = "select BookDate as bd, ItemName as iname, sum(Quantity) as qty from booking inner join orders on booking.ReservationID=orders.ReservationId ";
    $sql .= " inner join orderdetails on orders.OrderID = orderDetails.OrderID inner join items on ";
    $sql .= " items.ItemID=orderdetails.ItemID group by BookDate, items.ItemID, ItemName order by qty desc, iname";
    $statement = $pdo->prepare($sql);
    $result = $statement->execute();
    while ($row = $statement->fetch()) {
        echo "<tr>";
        echo "<td>".$row['bd']."</td>";
        echo "<td>".$row['iname']."</td>";
        echo "<td>".$row['qty']."</td>";
        echo "</tr>";
    }

        ?>
</div> 
</body>
</html>