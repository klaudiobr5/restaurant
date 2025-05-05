<?php
function addSchedule($pdo){
    ?>

    <table class="table table-bordered">
        <thead>
        <tr><th colspan="5">Schedule for <?php echo $_POST['start'];?></th></tr>
        <tr>
        <th>Employee</th><th>Type</th><th>Start Time</th><th>End Time</th><th>Delete</th>
        </tr>
        <tbody>
            <?php
                //get all schedules for the day selected
                $sql = "select * from schedules inner join users on users.Username = schedules.Username where scheduleDate=:sd order by StaffTypeId, users.Username";
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':sd', $_POST['start'], PDO::PARAM_STR);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                    echo "<tr><td>".$row['Fullname']."</td>";
                    if ($row['StaffTypeID']==3){
                        echo "<td>Wait</td>";
                    }
                    else if ($row['StaffTypeID']==4){
                        echo "<td>Kitchen</td>";
                    }
                    else if ($row['StaffTypeID']==5){
                        echo "<td>Inventory</td>";
                    }
                    echo "<td>".$row['ScheduleStart']."</td>";
                    echo "<td>".$row['ScheduleEnd']."</td>";
                    /* show one button to delete schedule */
                    echo "<form method='post'><td>";
                    echo "<input type='hidden' name='id' value='".$row['Username']."'>";
                    echo "<input type='hidden' name='resDate' value='".$_POST['start']."'>";
                    echo "<input type='hidden' name='start' value='".$_POST['start']."'>";
                    echo "<input type='submit' class='btn btn-primary' value='Delete' name='delete'></td></form>";
                    
                    echo "</tr>";
                }
            ?>
        </tbody>
		</table>
        <table class="table table-bordered">
        <thead>
        <tr><th colspan="5">Employess</th></tr>
        <tr>
        <th>Employee</th><th>Type</th><th>Start Time</th><th>End Time</th><th>Add</th>
        </tr>
        <tbody>
            <?php
                //get all employees
                $sql = "select * from users where staffTypeId>2 and Username not in ";
                //not in schedules
                $sql .= " (select username from schedules where scheduleDate=:sd) and Username not in ";
                //and they do not have time off that day
                $sql .= " (select username from timeoff where :sd between OffDateStart and OffDateEnd and approve=2)";
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':sd', $_POST['start'], PDO::PARAM_STR);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                    echo "<tr><td>".$row['Fullname']."</td>";
                    if ($row['StaffTypeID']==3){
                        echo "<td>Wait</td>";
                    }
                    else if ($row['StaffTypeID']==4){
                        echo "<td>Kitchen</td>";
                    }
                    else if ($row['StaffTypeID']==5){
                        echo "<td>Inventory</td>";
                    }
                    echo "<form method='post'><td> <input id=\"startTime\" list=\"times\" type=\"time\" name=\"startTime\" value=\"18:00\" step=\"3600\">";
                    ?>
                    <datalist id="times">

                        <option value="18:00:00">
                        <option value="19:00:00">
                        <option value="20:00:00">
                        <option value="21:00:00">
                        <option value="22:00:00">
                        <option value="23:00:00">
                        <option value="23:59:00">
                    </datalist><?php
                    echo "</td>";
                    echo "<td> <input id=\"endTime\" list=\"times\" type=\"time\" name=\"endTime\" value=\"18:00\" step=\"3600\">";
                    echo "</td>";
                    /* show one button to add schedule */
                    echo "<input type='hidden' name='id' value='".$row['Username']."'>";
                    echo "<input type='hidden' name='resDate' value='".$_POST['start']."'>";
                    echo "<input type='hidden' name='start' value='".$_POST['start']."'>";
                    echo "<td><input type='submit' class='btn btn-primary' value='Add' name='toadd'></td></form>";
                    
                    echo "</tr>";
                }
            ?>
        </tbody>
		</table>

<?php
}

function addReservation($pdo){
    ?>
    <form method='post'>
    <table class="table table-bordered">
        <thead>
        <tr><th colspan="5">Reservation for <?php echo $_POST['start'];?></th></tr>
        <tr>
        <th>Table</th><th>Max Number of Guests</th><th>Number of Guests</th><th>Time</th><th>Reserve</th>
        </tr>
        <tbody>
            <?php
                //get all schedules for the day selected
                $sql = "select tables.TableNumber as tbl, MaxNumberOfGuest, ReservationId  from tables left join booking on tables.TableNumber = booking.TableNumber and BookDate=:sd";
                $statement = $pdo->prepare($sql);
                $statement->bindParam(':sd', $_POST['start'], PDO::PARAM_STR);
                $result = $statement->execute();
                while ($row = $statement->fetch()) {
                   
                    echo "<tr><td>".$row['tbl']."</td>";
                    echo "<td>".$row['MaxNumberOfGuest']."</td>";
                    echo "<td><input type='number' min='1' max='".$row['MaxNumberOfGuest']."' value='".$row['MaxNumberOfGuest']."' name='nog'></td>";
                    //echo "<td> <input id=\"startTime\" list=\"times\" type=\"time\" name=\"startTime\" value=\"18:00\" step=\"3600\">";
                    ?>
                    <td><select name="times1">

                        <option value="18:00"> 18:00</option>
                        <option value="19:00">19:00</option>
                        <option value="20:00">20:00</option>
                        <option value="21:00">21:00</option>
                        <option value="22:00">22:00</option>
                        <option value="23:00">23:00</option>
                </select>
                    <?php
                    echo "</td>";
                    echo "<td>";
                    echo "<input type='hidden' name='start' value='".$_POST['start']."'>";
                    if (!isset($row['ReservationId']))
                        echo "<input type='checkbox' value='".$row['tbl']."' name=\"tables1[]\"></td>";
                    else
                        echo "<input type='checkbox' value='".$row['tbl']."' name=\"tables1[]\" disabled></td>";
                    
                    echo "</tr>";
                }
            ?>
        </tbody>
		</table>
        <div class="form-group">
        <input type="submit" value="Reserve" class='btn btn-primary btn-lg btn-block' name='addReserve'></div>
        </form> 
<?php
}
    ?>