<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Documentation Files Retrieval System</title>
        <link href="css.css" rel="stylesheet" type="text/css"/>
    </head>
    <?php
        include_once './mysqli_connection.php';
        $errormsg = "";
        $customername = "";
        $email = "";
        $address = "";
        $phone = "";
        
        function valid_email($email) {
            global $connection;
            $statement = mysqli_prepare($connection, "SELECT email FROM customer WHERE email = ? UNION SELECT email FROM staff WHERE email = ?");
            mysqli_stmt_bind_param($statement, "ss", $email, $email);
            mysqli_stmt_execute($statement);
            mysqli_stmt_store_result($statement);
            if(mysqli_stmt_num_rows($statement) > 0) {
                mysqli_stmt_close($statement);
                return FALSE;
            }
            return TRUE;
        }

        if(isset($_POST["create"])) {
            $customername = $_POST["customername"];
            $email = trim($_POST["email"]);
            $address = $_POST["address"];
            $phone = $_POST["phone"];
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            
            if(valid_email($email)) {
                $statement = mysqli_prepare($connection, "INSERT INTO customer(customername,email,password,address,phone) VALUES(?,?,?,?,?)");
                mysqli_stmt_bind_param($statement, "sssss", $customername, $email, $password, $address, $phone);
                mysqli_stmt_execute($statement);
                
                $errormsg = "success";
                $customername = "";
                $email = "";
                $address = "";
                $phone = "";
            }
            else {
                $errormsg = "Email already exist.";
            }
        }
    ?>
    <body>
        <div class="menu">
            <a href="index.php" style="margin-left: 0px;">Home</a>
            <a href="register.php" style="margin-left: 37px;">Register</a>
            <a href="" style="margin-left: 37px;">Contact</a>
            <a href="" style="margin-left: 37px;">About</a>
        </div>
        <div class="holder">
            <form name="frm" method="POST">
                <fieldset>
                    <legend>Staff Registration</legend>
                    Staff Name <input type="text" name="customername" value="<?php echo $customername; ?>" required maxlength="30" pattern="[a-zA-Z][a-zA-Z ]+" title="Customer name only in letter with space" autofocus /><br><br>
                    Email <input type="email" name="email" value="<?php echo $email; ?>" required maxlength="50" title="Valid email to use in log in" /><br><br>
                    Password <input type="password" name="password" value="" required maxlength="20" pattern="\w+" title="Password" onchange="frm.cpassword.pattern = this.value;" /><br><br>
                    Retype Password <input type="password" name="cpassword" value="" required maxlength="20" title="Retype password same as above password" /><br><br>
                    Address <textarea name="address" required maxlength="100"><?php echo $address; ?></textarea><br><br>
                    Phone <input type="text" name="phone" value="<?php echo $phone; ?>" required maxlength="30" pattern="[0-9][0-9\-, ]+" title="Phone no only allow number, hyphen and comma." /><br><br>
                    <input type="submit" value="Create" name="create" style="margin-right: 7px;" />
                    <input type="submit" value="Cancel" name="cancel"  formnovalidate />
                    <label style="color: red;"><?php echo $errormsg; ?></label>
                    <?php if($errormsg == "success") {
                        $errormsg = "";
                        header("Refresh: 3; Url=index.php");
                    } ?>
                </fieldset>
            </form>
        </div>
    </body>
</html>
