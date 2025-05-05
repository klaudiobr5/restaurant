<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse w-100 order-1 order-md-0 dual-collapse2" id="navbarTogglerDemo02">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="centralPage.php">Home</a>
      </li>
      <?php  
      if ($_SESSION["type"]!="2") { //if user is not customer?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Employees</a>
        <div class="dropdown-menu">
        <?php  
        if ($_SESSION["type"]=="1") { ?>
            <a class="dropdown-item" href="addEmployee.php">Add Employee</a>
          <?php } ?>
          
            <a class="dropdown-item" href="timeOff.php">Time Off</a>
          
            <a class="dropdown-item" href="schedule.php">Schedule </a>
          
        </div>
      </li>
      <?php } ?>  
      <li class="nav-item">
        <a class="nav-link" href="reservation.php">Reservation</a>
      </li>
      <?php  
        if ($_SESSION["type"]=="3") { //Wait staff ?>
      <li class="nav-item">
        <a class="nav-link" href="order.php">Order</a>
      </li>
      <?php 
        }
        ?>
        <?php  
      if ($_SESSION["type"]=="5") { //if user inventory stuff?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="livestock.php">Live Stock</a>
            <a class="dropdown-item" href="stockalert.php">Stock Alert</a>          
        </div>
      </li>
      <?php } ?> 
      <?php  
      if ($_SESSION["type"]=="1") { //if user is adminr?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Reports</a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="livestock.php">Live Stock</a>
            <a class="dropdown-item" href="stockalert.php">Stock Alert</a>
            <a class="dropdown-item" href="sales.php">Sales </a>
          
        </div>
      </li>
      <?php } ?> 
    </ul>
	</div>

	<div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="userLogout.php">
				<i class="fa  fa-user"></i>
        Logout</a></li>   
        </ul>

    </div>
	
  </div>
</nav>