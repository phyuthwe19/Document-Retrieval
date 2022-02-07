<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Documentation Files Retrieval System</title>
        <link href="css.css" rel="stylesheet" type="text/css"/>
        
        <link rel="stylesheet" href="js/Datepicker/themes/ui-lightness/jquery.ui.all.css" />
        <script src="js/jquery-2.1.4.js"></script>
        
    </head>
    <?php
        session_start();
        require_once './mysqli_connection.php';
        $categoryid = "";
       
        $errormsg = "";
        $result_list = "";
        
        if(isset($_POST["search"])) {            
            $categoryid = $_POST["categoryid"];            
            $statement = $connection->prepare("SELECT * FROM outgoing_message om, category c WHERE om.categoryid = c.categoryid AND om.categoryid = ?");
            $statement->bind_param("i", $categoryid);
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
        
        <div class="holder" style="width: 1000px;">
            <form name="frm" method="POST">  
                <fieldset> 
                    <legend>Search</legend>
                    <a href="search_date.php"><div class="category">By Date</div></a>
                    <a href="search_month.php"><div class="category">By Month</div></a>
                    <a href="search_year.php"><div class="category">By Year</div></a>
                    <a href="search_category.php"><div class="category">By Category</div></a>
                    
                </fieldset><br><br>

            </form>    

        </div>

        
        <div class="holder">
                <form name="frm" method="POST">      
                    <fieldset>
                        <legend>Search By Category</legend>
                        Category Name <select name="categoryid" required><option value="">Select Category</option>
                            <?php
                            $statement = mysqli_prepare($connection, "SELECT * FROM category ORDER BY categoryname");
                            mysqli_stmt_execute($statement);
                            mysqli_stmt_bind_result($statement, $cid, $cname);
                            while (mysqli_stmt_fetch($statement)) {
                                $s = ($categoryid == $cid) ? "selected" : "";
                                echo "<option value='$cid' $s>$cname</option>";
                            }
                            mysqli_stmt_close($statement);
                            ?>    
                        </select><br><br>

                        <input type="submit" value="Cancel" name="cancel"  formnovalidate style="margin-right: 7px;"/>
                        <input type="submit" value="Search" name="search"  />
                        <label style="color: red;"><?php echo $errormsg; ?></label>

                    </fieldset><br><br>
                </form>    
            </div>
        <div class="holder" style="width: 900px; height: 500px; overflow:auto;">
                <form name="frm" method="POST">
                    <fieldset>

                        <table style="width: 850px;">
                                <tr>
                                    <th>No</th>
                                    <th>Outgoing Date</th>
                                    <th>ID</th>
                                    <th>To Department</th>
                                    <th>Letter No</th>
                                    <th>Description</th>
                                    <th>Remark</th>
                                    <th>File Name</th>

                                </tr>

                                <?php
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
                                        $no++;
                                    }
                                }
                                ?>


                        </table>
                </fieldset>
            </form>
        </div>
        <br><br>
    </body>
</html>
