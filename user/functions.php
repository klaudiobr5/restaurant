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
                $sql = "select * from schedules inner join users on users.Username = schedules.Username where scheduleDate=:sd group by StaffTypeId order by users.Username";
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
                    //echo "<select name='startTime'>";
                    //for ($i=18; $i<24; $i++){
                    //    echo "<option value='".$i."'>".$i."</option>";
                    //}
                    //echo "</select></td>";
                    //echo "<td>";
                    echo "<td> <input id=\"endTime\" list=\"times\" type=\"time\" name=\"endTime\" value=\"18:00\" step=\"3600\">";
                    //echo "<select name='endTime'>";
                    //for ($i=18; $i<24; $i++){
                    //    echo "<option value='".$i."'>".$i."</option>";
                    //}
                    //echo "</select></td>";
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
    ?>