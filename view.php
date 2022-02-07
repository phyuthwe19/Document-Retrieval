<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Documentation Files Retrieval System</title>
        <link href="css.css" rel="stylesheet" type="text/css"/>
        
        <link rel="stylesheet" href="js/Datepicker/themes/ui-lightness/jquery.ui.all.css" />
        <script src="js/jquery-2.1.4.js"></script>
        <script src="js/Datepicker/ui/minified/jquery.ui.core.min.js"></script>
        <script src="js/Datepicker/ui/minified/jquery.ui.datepicker.min.js"></script>
        <script>
            $(function() {
                $("#weekly").datepicker();
                $("#weekly").datepicker("option", "dateFormat", "yy-mm-dd");
                $("#weekly").datepicker("option", "showWeek", true);
                $("#weekly").datepicker("option", "firstDay", 1);
                $("#weekly").datepicker("option", "changeMonth", true);
                $("#weekly").datepicker("option", "changeYear", true);

                $("#weekly").datepicker("option", "onSelect",
                    function(value, date) {
                        var week = $.datepicker.iso8601Week (
                            new Date(date.selectedYear, date.selectedMonth, date.selectedDay));

                        //calculate first day of selected week
                        var simple = new Date(date.selectedYear, 0, 1 + (week - 1) * 7);
                        var dow = simple.getDay();
                        var ISOweekStart = simple;
                        if (dow <= 4)
                            ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
                        else
                            ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
                        var startdate = ISOweekStart.toLocaleFormat('%Y-%m-%d');

                        //calculate last day of selected week
                        var enddate = new Date(startdate);
                        var dateadd = enddate.getTime() + 1000*60*60*24*6;
                        enddate.setTime(dateadd);

                        $(this).val(startdate + ' - ' + enddate.toLocaleFormat('%Y-%m-%d'));
                    }
                );

            });

            $(function() {
                $("#daily").datepicker();
                $("#daily").datepicker("option", "dateFormat", "yy-mm-dd");
                $("#daily").datepicker("option", "changeMonth", true);
                $("#daily").datepicker("option", "changeYear", true);
            });
        </script>
    </head>
    <?php
        session_start();
        require_once './mysqli_connection.php';
        $categoryid = "";
        $daily = date("Y-m-d");
        $errormsg = "";
        $result_list = "";
        
        if(isset($_POST["search"])) {            
            $daily = $_POST["daily"];            
            $statement = $connection->prepare("SELECT out_id,out_date,to_dept,letter_no,description,remark,file_name,categoryname FROM outgoing_message om, category c WHERE om.categoryid = c.categoryid AND om.out_date = ?");
            $statement->bind_param("s", $daily);
            $statement->execute();
            $result = $statement->get_result();
            $result_list = $result->fetch_all();
            $result->free();
            $statement->close();
            
        }
    ?>
    <body>
        <div class="menu">
            <a href="outgoing_message.php">Outgoing Letter</a>
            <a href="edit_delete_message.php">Edit or Delete </a>
            <a href="search_date.php" style="margin-left: 20px;">Search</a>
            <a href="view.php" style="margin-left: 20px;">View </a>
            <a href="logout.php" style="margin-left: 20px;">Logout</a>
        </div>     
        
        <div class="holder" style="width: 1000px; height: 500px; overflow:auto ">
            <form name="frm" method="POST">
                <fieldset>
                    <legend >Outgoing Letter List</legend>
                    <table style="width: 950px;  ">
                        <tr>
                            <th>No</th>
                            <th>Outgoing Date</th>
                            <th>ID</th>
                            <th>To Department</th>
                            <th>Letter No</th>
                            <th>Description</th>
                            <th>Remark</th>
                            <th>File Name</th>
                            <th>Category Name</th>
                        </tr>
                        <?php
                            $daily = date("Y-m-d");            
                            $statement = $connection->prepare("SELECT out_id,out_date,to_dept,letter_no,description,remark,file_name,categoryname FROM outgoing_message om, category c WHERE om.categoryid = c.categoryid AND om.out_date = ?");
                            $statement->bind_param("s", $daily);
                            $statement->execute();
                            $result = $statement->get_result();
                            $result_list = $result->fetch_all();
                            $result->free();
                            $statement->close();
                            
                            if (!empty($result_list)) {
                                    $no = 1;
                                    for ($i = 0; $i < sizeof($result_list); $i++) {
                                        echo "<tr><td>$no</td><td>" . $result_list[$i][1] . "</td>";
                                        echo "<td>" . $result_list[$i][0] . "</td>";
                                        echo "<td>" . $result_list[$i][2] . "</td>";
                                        echo "<td>" . $result_list[$i][3] . "</td>";
                                        echo "<td>" . $result_list[$i][4] . "</td>";
                                        echo "<td>" . $result_list[$i][5] . "</td>";
                                        echo "<td>" . $result_list[$i][6] . "</td>";
                                        echo "<td>" . $result_list[$i][7] . "</td>";
                                        $no++;
                                    }
                                }
                            
                        
                            
                        ?>
                    </table>
                    <label style="color: red;"><?php echo $errormsg; ?></label>
                </fieldset><br><br>
            </form>
        </div>
        <br><br>
    </body>
</html>
