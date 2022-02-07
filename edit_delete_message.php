<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Documentation Files Retrieval System</title>
        <link href="css.css" rel="stylesheet" type="text/css"/>
        
        <script>
            function readURL(input) {
                var url = input.value;
                var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
                if (input.files && input.files[0]&& (ext === "pdf" || ext === "docx" || ext === "xlsx" || ext === "jpeg")) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById("image").src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
                else {
                    document.getElementById("image").src = 'hairimg/preview.png';
                    input.value = "";
                    alert("You must select an file of type jpg, docx, pdf, xlsx!");
                }
            }
        </script>
        
        <link rel="stylesheet" href="js/Datepicker/themes/ui-lightness/jquery.ui.all.css" />
        <script src="js/jquery-2.1.4.js"></script>
        <script src="js/Datepicker/ui/minified/jquery.ui.core.min.js"></script>
        <script src="js/Datepicker/ui/minified/jquery.ui.datepicker.min.js"></script>
        <script>
            

            $(function() {
                $("#out_date").datepicker();
                $("#out_date").datepicker("option", "dateFormat", "yy-mm-dd");
                $("#out_date").datepicker("option", "changeMonth", true);
                $("#out_date").datepicker("option", "changeYear", true);
            });
        </script>
        
    </head>
    <?php
        session_start();
        require_once './mysqli_connection.php';
        $errormsg = "";
        $out_id = "";
        $out_date = "";
        $to_dept = "";
        $letter_no = "";
        $description = "";
        $remark = "";
        $file_name = "";
        $categoryid = "";
        
        if(isset($_POST["save"])) {
            $out_date = $_POST["out_date"];
            $to_dept = $_POST["to_dept"];
            $letter_no = $_POST["letter_no"];
            $description = trim($_POST["description"]);
            $remark = $_POST["remark"];
            $file_name = $_POST["file_name"];            
            $categoryid = $_POST["categoryid"];
            
            
            if(isset($_SESSION["out_id"])) {
                if(empty($_FILES["file_name"]["name"])) {
                    $statement = mysqli_prepare($connection, "UPDATE outgoing_message SET out_date=?,to_dept=?,letter_no=?,description=?,remark=?,categoryid=? WHERE out_id=?");
                    mysqli_stmt_bind_param($statement, "sssssii", $out_date, $to_dept, $letter_no, $description, $remark, $categoryid, $_SESSION["out_id"]);
                    mysqli_stmt_execute($statement);
                }
                else {
                    $file_name = $_SESSION["out_id"] . "." . pathinfo($_FILES["file_name"]["name"], PATHINFO_EXTENSION);
                    $statement = mysqli_prepare($connection, "UPDATE outgoing_message SET out_date=?,to_dept=?,letter_no=?,description=?,remark=?,file_name=?,categoryid=? WHERE out_id=?");
                    mysqli_stmt_bind_param($statement, "ssssssii", $out_date, $to_dept, $letter_no, $description,$remark, $file_name, $categoryid, $_SESSION["out_id"]);
                    mysqli_stmt_execute($statement);
                    
                    $img = glob($_SERVER["DOCUMENT_ROOT"] . "/Document_Retrieval/Outbox/" . $_SESSION["out_id"] . ".*");
                    foreach ($img as $i) {
                        unlink($i);
                    }
                    move_uploaded_file($_FILES["file_name"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . "/Document_Retrieval/Outbox/" . $file_name);
                }
            }
            else {
                $statement = mysqli_prepare($connection, "INSERT INTO outgoing_message(out_date,to_dept,letter_no,description,remark,categoryid) VALUES(?,?,?,?,?,?)");
                mysqli_stmt_bind_param($statement, "sssssi", $out_date, $to_dept, $letter_no, $description, $remark, $categoryid);
                mysqli_stmt_execute($statement);
                $out_id = mysqli_insert_id($connection);
                mysqli_stmt_close($statement);
                $file_name = $out_id . "." . pathinfo($_FILES["file_name"]["name"], PATHINFO_EXTENSION);
                
                $statement = mysqli_prepare($connection, "UPDATE outgoing_message SET file_name=? WHERE out_id=?");
                mysqli_stmt_bind_param($statement, "si", $file_name, $out_id);
                mysqli_stmt_execute($statement);
                
                move_uploaded_file($_FILES["file_name"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . "/Document_Retrieval/Outbox/" . $file_name);
            }
            mysqli_stmt_close($statement);
            $errormsg = "";
            $out_id = "";
            $out_date = "";
            $to_dept = "";
            $letter_no = "";
            $description = "";
            $remark = "";
            $file_name = "";
            $categoryid = "";
            unset($_SESSION["out_id"]);
        }
        
        if(isset($_POST["edit"])) {
            $temp = array_keys($_POST["edit"]);
            $out_id = array_pop($temp);
            $statement = mysqli_prepare($connection, "SELECT * FROM outgoing_message WHERE out_id = ?");
            mysqli_stmt_bind_param($statement, "i", $out_id);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $out_id, $out_date,$to_dept,$letter_no, $description, $remark, $file_name, $categoryid);
            mysqli_stmt_fetch($statement);
            mysqli_stmt_close($statement);
            $_SESSION["out_id"] = $out_id;
        }
        
        if(isset($_POST["delete"])) {
            $temp2 = array_keys($_POST["delete"]);
            $out_id = array_pop($temp2);
            $statement = mysqli_prepare($connection, "DELETE FROM outgoing_message WHERE out_id = ?");
            mysqli_stmt_bind_param($statement, "i", $out_id);
            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);
            
            $img = glob($_SERVER["DOCUMENT_ROOT"] . "/Document_Retrieval/Outbox/" . $out_id . ".*");
            foreach ($img as $i) {
                unlink($i);
            }
            
            $errormsg = "";
            $out_id = "";
            $out_date = "";
            $to_dept = "";
            $letter_no = "";
            $description = "";
            $remark = "";
            $file_name = "";
            $categoryid = "";
            unset($_SESSION["out_id"]);
        }
        
        if(isset($_POST["cancel"])) {
            unset($_SESSION["out_id"]);
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
        <div class="holder" style="width: 1100px; height: 500px; overflow:auto">
            <form name="frm" method="POST">
                <fieldset>
                    <legend>Outgoing Letter List</legend>
                    <table style="width: 780px;">
                        <tr>
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
                            $statement = mysqli_prepare($connection, "SELECT * FROM outgoing_message om, category c WHERE om.categoryid = c.categoryid ORDER BY out_id");
                            mysqli_stmt_execute($statement);
                            mysqli_stmt_bind_result($statement, $oid, $odate,$todep, $lNo, $desc, $rem, $fname, $catid, $catid, $cname);
                            while(mysqli_stmt_fetch($statement)) {
                                echo "<tr><td>$odate</td><td>$oid</td><td>$todep</td><td>$lNo</td><td>$desc</td><td>$rem</td>";
                                //echo "<td><img src='hairimg/$img' style='width:30px; height:30px;'></td>";
                                echo "<td>$fname</td><td>$cname</td><td><input type='submit' name='edit[$oid]' value='Edit' style='width:50px;'></td>";
                                echo "<td><input type='submit' name='delete[$oid]' value='Delete' style='width:50px;' /></td></tr>";                                
                            }
                            mysqli_stmt_close($statement);
                        ?>
                    </table>
                    <label style="color: red;"><?php echo $errormsg; ?></label>
                </fieldset><br><br>
            </form>
        </div><br><br>
        <div class="holder">
            <form name="frm2" method="POST" enctype="multipart/form-data">
                <fieldset>
                    <legend>Update Outgoing Letter</legend>
                    Outgoing Message ID <input type="text" name="out_id" value="<?php echo "$out_id"; ?>" readonly /><br><br>
                    Outgoing Date <input type="text" name="out_date" value="<?php echo $out_date; ?>" readonly /><br><br>
                    To Department <input type="text" name="to_dept" value="<?php echo $to_dept; ?>" /><br><br>
                    Letter No <textarea name="letter_no" required maxlength="300"><?php echo $letter_no; ?></textarea><br><br>
                    Description <textarea name="description" required maxlength="500"><?php echo $description; ?></textarea><br><br>
                    Remark <input type="text" name="remark" value="<?php echo $remark; ?>" /><br><br>
                    File Name<input type="text" name="file_name" value="<?php echo $file_name; ?>" /><br><br>
                    Change File <input type="file" name="file_name" value="" <?php echo isset($_SESSION["out_id"]) ? "" : ""; ?> onchange="readURL(this);" /><br><br>
                    Category Name <select name="categoryid" required><option value="">Select Category</option>
                    <?php
                        $statement = mysqli_prepare($connection, "SELECT * FROM category ORDER BY categoryname");
                        mysqli_stmt_execute($statement);
                        mysqli_stmt_bind_result($statement, $cid, $cname);
                        while(mysqli_stmt_fetch($statement)) {
                            $s = ($categoryid == $cid)? "selected": "";
                            echo "<option value='$cid' $s>$cname</option>";
                        }
                        mysqli_stmt_close($statement);
                    ?>    
                    </select><br><br>
                    <input type="submit" value="Update" name="save"  style="margin-right: 7px;" />
                    <input type="submit" value="Cancel" name="cancel" formnovalidate />
                    <label style="color: red;"><?php echo $errormsg; ?></label>                    
                    
                </fieldset>
            </form>
        </div>
        
    </body>
</html>
