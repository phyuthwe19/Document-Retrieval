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
                if (input.files && input.files[0] && (ext === "pdf" || ext === "docx" || ext === "xlsx" || ext === "jpeg")) {
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


            $(function () {
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
    $to_dept = "";
    $letter_no = "";
    $description = "";
    $remark = "";
    $file_name = "";
    $categoryid = "";

    if (isset($_POST["save"])) {
        $out_id = $_POST["out_id"];
        if (!empty($_POST["out_date"])) {
            $out_date = $_POST["out_date"];
            $to_dept = $_POST["to_dept"];
            $letter_no = $_POST["letter_no"];
            $description = trim($_POST["description"]);
            $remark = $_POST["remark"];
            //$file_name = $_POST["file_name"];
            $categoryid = $_POST["categoryid"];


            if (isset($_SESSION["out_id"])) {
                if (empty($_FILES["file_name"]["name"])) {
                    $statement = mysqli_prepare($connection, "UPDATE outgoing_message SET out_date=?,to_dept=?,letter_no=?,description=?,remark=?,categoryid=? WHERE out_id=?");
                    mysqli_stmt_bind_param($statement, "sssssii", $out_date, $to_dept, $letter_no, $description, $remark, $categoryid, $_SESSION["out_id"]);
                    mysqli_stmt_execute($statement);
                } else {
                    $file_name = $_SESSION["out_id"] . "." . pathinfo($_FILES["file_name"]["name"], PATHINFO_EXTENSION);
                    $statement = mysqli_prepare($connection, "UPDATE outgoing_message SET out_date=?,to_dept=?,letter_no=?,description=?,remark=?,file_name=?,categoryid=? WHERE out_id=?");
                    mysqli_stmt_bind_param($statement, "ssssssi", $productname, $description, $price, $categoryid, $image, $_SESSION["productid"]);
                    mysqli_stmt_execute($statement);

                    $img = glob($_SERVER["DOCUMENT_ROOT"] . "/Document_Retrieval/Outbox/" . $_SESSION["out_id"] . ".*");
                    foreach ($img as $i) {
                        unlink($i);
                    }
                    move_uploaded_file($_FILES["file_name"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . "/Document_Retrieval/Outbox/" . $file_name);
                }
            } else {
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
            $to_dept = "";
            $letter_no = "";
            $description = "";
            $remark = "";
            $filename = "";
            $categoryid = "";
            unset($_SESSION["out_id"]);
        } else {
            $errormsg = "Please choose outgoing date.";
        }
    }

    if (isset($_POST["edit"])) {
        $out_id = array_pop(array_keys($_POST["edit"]));
        $statement = mysqli_prepare($connection, "SELECT * FROM outgoing_message WHERE out_id = ?");
        mysqli_stmt_bind_param($statement, "i", $out_id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_bind_result($statement, $out_id, $out_date, $to_dept, $letter_no, $description, $remark, $file_name, $categoryid);
        mysqli_stmt_fetch($statement);
        mysqli_stmt_close($statement);
        $_SESSION["out_id"] = $out_id;
    }

    if (isset($_POST["delete"])) {
        $outid = array_pop(array_keys($_POST["delete"]));
        $statement = mysqli_prepare($connection, "DELETE FROM outgoing_message WHERE out_id = ?");
        mysqli_stmt_bind_param($statement, "i", $out_id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        $img = glob($_SERVER["DOCUMENT_ROOT"] . "/Document_Retrieval/Outbox/" . $out_id . ".*");
        foreach ($img as $i) {
            unlink($i);
        }

        $errormsg = "";
        $to_dept = "";
        $letter_no = "";
        $description = "";
        $remark = "";
        $filename = "";
        $categoryid = "";
        unset($_SESSION["out_id"]);
    }

    if (isset($_POST["cancel"])) {
        unset($_SESSION["out_id"]);
    }
    ?>
    <body>
        <div class="menu">
            <a href="outgoing_message.php">Outgoing Letter</a>
            <a href="edit_delete_message.php">Edit or Delete </a>
            <a href="search_date.php" style="margin-left: 20px;">Search </a>
            <a href="view.php" style="margin-left: 20px;">View  </a>
            <a href="logout.php" style="margin-left: 20px;">Logout</a>
        </div>
        <div class="holder">
            <form name="frm2" method="POST" enctype="multipart/form-data">
                <fieldset>
                    <legend>New Outgoing Letter</legend>
                    Outgoing Letter ID <input type="text" name="out_id" value="<?php
                    $statement = mysqli_prepare($connection, "SELECT max(out_id) FROM outgoing_message");
                    mysqli_stmt_execute($statement);
                    mysqli_stmt_bind_result($statement, $oid);
                    mysqli_stmt_fetch($statement);
                    $oid++;
                    echo "$oid";
                    mysqli_stmt_close($statement);
                    ?>" readonly /><br><br>
                    Outgoing Date <input type="text" id="out_date" name="out_date" value="" onkeypress="return false;" /><br><br>
                    To Department <input type="text" name="to_dept" value="<?php echo $to_dept; ?>" /><br><br>
                    Letter No <textarea name="letter_no" maxlength="300"><?php echo $letter_no; ?></textarea><br><br>
                    Description <textarea name="description" maxlength="500"><?php echo $description; ?></textarea><br><br>
                    Remark <input type="text" name="remark" value="<?php echo $remark; ?>" maxlength="100" /><br><br>
                    File <input type="file" name="file_name" value="" <?php echo isset($_SESSION["out_id"]) ? "" : ""; ?> onchange="readURL(this);" /><br><br>
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
                    <input type="submit" value="Save" name="save"  style="margin-right: 7px;" />
                    <input type="submit" value="Cancel" name="cancel" formnovalidate />
                    <label style="color: red;"><?php echo $errormsg; ?></label>                    

                </fieldset>
            </form>
        </div><br><br>
        <div class="holder" style="width: 1100px; height: 800px; overflow:auto ">
            <form name="frm" method="POST">
                <fieldset>
                    
                    <table style="width: 790px;  ">
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
                        if (isset($_GET['page_no']) && $_GET['page_no'] != "") {
                            $page_no = $_GET['page_no'];
                        } else {
                            $page_no = 1;
                        }

                        $total_records_per_page = 6;
                        $offset = ($page_no - 1) * $total_records_per_page;
                        $previous_page = $page_no - 1;
                        $next_page = $page_no + 1;
                        $adjacents = "2";

                        $result_count = mysqli_query($connection, "SELECT COUNT(*) As total_records FROM `outgoing_message`");
                        $total_records = mysqli_fetch_array($result_count);
                        $total_records = $total_records['total_records'];
                        $total_no_of_pages = ceil($total_records / $total_records_per_page);
                        $second_last = $total_no_of_pages - 1;

                        
                        $result = mysqli_query($connection, "SELECT * FROM outgoing_message om, category c WHERE om.categoryid = c.categoryid ORDER BY out_id LIMIT $offset, $total_records_per_page");
                        while ($row = mysqli_fetch_array($result)) {
                            $filelocation = "/Document_Retrieval/Outbox/".$row['file_name'];
                            echo "<tr>
                                <td>" . $row['out_date'] . "</td>
                                <td>" . $row['out_id'] . "</td>
                                <td>" . $row['to_dept'] . "</td>
                                <td>" . $row['letter_no'] . "</td>
                                <td>" . $row['description'] . "</td>                                    
                                <td>" . $row['remark'] . "</td>
                                <td> <a href=$filelocation> " . $row['file_name'] . "</td>
                                <td>" . $row['categoryname'] . "</td>    
                                </tr>";
                        }
                        mysqli_close($connection);

//                        $statement = mysqli_prepare($connection, "SELECT * FROM outgoing_message om, category c WHERE om.categoryid = c.categoryid ORDER BY out_id");
//                        mysqli_stmt_execute($statement);
//                        mysqli_stmt_bind_result($statement, $oid, $odate, $todep, $lNo, $desc, $remark, $fname, $catid, $catid, $cname);
//                        while (mysqli_stmt_fetch($statement)) {
//                            echo "<tr><td>$odate</td><td>$oid</td><td>$todep</td><td>$lNo</td><td>$desc</td><td>$remark</td>";
//                            //echo "<td><img src='hairimg/$img' style='width:30px; height:30px;'></td>";
//                            $filelocation = "/Document_Retrieval/Outbox/" . $file_name;
//                            echo "<td> <a href=$filelocation>" . $fname . "</a></td><td>$cname</td>";
//                        }
//                        mysqli_stmt_close($statement);
                        ?>
                    </table>

                    <div style='padding: 10px 20px 0px; border-top: dotted 1px #CCC;'>
                        <strong>Page <?php echo $page_no . " of " . $total_no_of_pages; ?></strong>
                    </div>

                    <ul class="pagination">
                        <?php // if($page_no > 1){ echo "<li><a href='?page_no=1'>First Page</a></li>"; }  ?>

                        <li <?php
                        if ($page_no <= 1) {
                            echo "class='disabled'";
                        }
                        ?>>
                            <a <?php
                            if ($page_no > 1) {
                                echo "href='?page_no=$previous_page'";
                            }
                            ?>>Previous</a>
                        </li>

                        <?php
                        if ($total_no_of_pages <= 10) {
                            for ($counter = 1; $counter <= $total_no_of_pages; $counter++) {
                                if ($counter == $page_no) {
                                    echo "<li class='active'><a>$counter</a></li>";
                                } else {
                                    echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                                }
                            }
                        } elseif ($total_no_of_pages > 10) {

                            if ($page_no <= 4) {
                                for ($counter = 1; $counter < 8; $counter++) {
                                    if ($counter == $page_no) {
                                        echo "<li class='active'><a>$counter</a></li>";
                                    } else {
                                        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                                    }
                                }
                                echo "<li><a>...</a></li>";
                                echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
                                echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                            } elseif ($page_no > 4 && $page_no < $total_no_of_pages - 4) {
                                echo "<li><a href='?page_no=1'>1</a></li>";
                                echo "<li><a href='?page_no=2'>2</a></li>";
                                echo "<li><a>...</a></li>";
                                for ($counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++) {
                                    if ($counter == $page_no) {
                                        echo "<li class='active'><a>$counter</a></li>";
                                    } else {
                                        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                                    }
                                }
                                echo "<li><a>...</a></li>";
                                echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
                                echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                            } else {
                                echo "<li><a href='?page_no=1'>1</a></li>";
                                echo "<li><a href='?page_no=2'>2</a></li>";
                                echo "<li><a>...</a></li>";

                                for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
                                    if ($counter == $page_no) {
                                        echo "<li class='active'><a>$counter</a></li>";
                                    } else {
                                        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                                    }
                                }
                            }
                        }
                        ?>

                        <li <?php
                        if ($page_no >= $total_no_of_pages) {
                            echo "class='disabled'";
                        }
                        ?>>
                            <a <?php
                            if ($page_no < $total_no_of_pages) {
                                echo "href='?page_no=$next_page'";
                            }
                            ?>>Next</a>
                        </li>
                        <?php
                        if ($page_no < $total_no_of_pages) {
                            echo "<li><a href='?page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
                        }
                        ?>
                    </ul>


                    <br /><br />

                    <label style="color: red;"><?php echo $errormsg; ?></label>
                </fieldset><br><br>
            </form>
        </div>
        <br><br>
    </body>
</html>
