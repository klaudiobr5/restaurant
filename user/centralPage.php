<?php
session_start();
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
    if(isset($_POST['addToBasket'])) {
        $pid=$_POST['pid'];
        if (isset($_SESSION['cart'])){
            if (array_key_exists($pid, $_SESSION['cart'])){
                echo '<script>alert("Υπάρχει ήδη στο καλάθι σας.")</script>';
            }
            else{
                $_SESSION['cart'][$pid]=$_POST['price'];
                echo '<script>alert("Προστέθηκε στο καλάθι σας.")</script>';
            }

        }
        else{
            $cart=array();
            $cart[$pid]=$_POST['price'];
            $_SESSION['cart']=$cart;
            echo '<script>alert("Προστέθηκε στο καλάθι σας.")</script>';
        }
    }
    else if(isset($_POST['addToWishList'])) {
        $pid=$_POST['pid'];
        $sql = "select * from wishlist where productId=:pid and userName=:userName";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':pid', $pid, PDO::PARAM_INT);
        $statement->bindParam(':userName', $_SESSION["username"], PDO::PARAM_STR);
        $result = $statement->execute();
        if (!$statement->fetch()){
            $sql = "insert into wishList (productid, userName) values (:pid, :userName)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':pid', $pid, PDO::PARAM_INT);
            $statement->bindParam(':userName', $_SESSION["username"], PDO::PARAM_STR);
            $result = $statement->execute();
            echo '<script>alert("Προστέθηκε στο wish list σας.")</script>';
        }
        else{
            echo '<script>alert("Υπάρχει ήδη στο wish list σας.")</script>';
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
        
        $sql = "select * from products where offer=1 and enabled=1 order by title";
        $statement = $pdo->prepare($sql);
        $result = $statement->execute();
        ?>
        <table class="table table-bordered">
        <thead>
        <tr>
          <th colspan="6">Προσφορές</th>
        </tr>
        <tr>
        <th>Τίτλος</th><th>Κονσόλα</th><th>Τιμή</th><th>Τύπος</th><th>Προβολή</th><th>Καλάθι</th>
        </tr>
        <tbody>
    <?php    
        while ($row = $statement->fetch()) {
            echo "<tr><td>".$row['title']."</td>";
            echo "<td>".$row['console']."</td>";
            echo "<td>".$row['price']."</td>";
            echo "<td>".$row['gameType']."</td>";
            echo "<td><form method='post' action='showGame.php'>";
            echo "<input type='hidden' name='pid' value='".$row['pId']."'>";
            echo "<input type='submit' class='btn btn-primary' value='Προβολή'></form></td>";
            if ($row['pieces']>0){
                echo "<td><form method='post' action=''>";
                echo "<input type='hidden' name='pid' value='".$row['pId']."'>";
                echo "<input type='hidden' name='price' value='".$row['price']."'>";
                echo "<input type='submit' value='Προσθήκη' class='btn btn-success' name='addToBasket'></form></td>";
            }
            else{
                echo "<td><form method='post' action=''>";
                echo "<input type='hidden' name='pid' value='".$row['pId']."'>";
                echo "<input type='hidden' name='price' value='".$row['price']."'>";
                echo "<input type='submit' value='WishList' class='btn btn-success' name='addToWishList'></form></td>";
            }
            echo "</tr>";
        }
    ?>
        </tbody>
    </table>
</div> 
</body>
</html>