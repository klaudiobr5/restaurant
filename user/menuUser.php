<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse w-100 order-1 order-md-0 dual-collapse2" id="navbarTogglerDemo02">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="centralPage.php">Home</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Employees</a>
        <div class="dropdown-menu">
        <?php  
        if ($_SESSION["type"]==1) { ?>
          <a class="dropdown-item" href="addEmployee.php">Add Employee</a>
          <?php } ?>
          <a class="dropdown-item" href="timeOff.php">Time Off</a>
          <a class="dropdown-item" href="addAdvLaptop.php">Schedule </a>
          
        </div>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" href="showAll.php">Reservation</a>
      </li>
      
      
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