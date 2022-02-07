<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Documentation Files Retrieval System</title>
        <link href="css.css" rel="stylesheet" type="text/css"/>
    </head>
    <?php
        session_start();
        require_once './mysqli_connection.php';
        $errormsg = "";
        
        if(isset($_POST["login"])) {
            $email = trim($_POST["email"]);
            $password = $_POST["password"];
            
            $statement = mysqli_prepare($connection, "SELECT staffid, password FROM staff WHERE email = ?");
            mysqli_stmt_bind_param($statement, "s", $email);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $id, $pwd);
            if(mysqli_stmt_fetch($statement)) {
                if(password_verify($password, $pwd)) {
                    if($email == "admin@miit.edu.mm")
                    {
                        $_SESSION["staffid"] = $id;
                        header("Location: outgoing_message.php");
                    }
                    else if($email == "user@miit.edu.mm"){
                        $_SESSION["staffid"] = $id;
                        header("Location: search_date_user.php");
                    }
                    
                }
                else {
                    $errormsg = "Invalid user.";
                }
            }
            else {
                mysqli_stmt_close($statement);
                $statement = mysqli_prepare($connection, "SELECT customerid, customername, password FROM customer WHERE email = ?");
                mysqli_stmt_bind_param($statement, "s", $email);
                mysqli_stmt_execute($statement);
                mysqli_stmt_bind_result($statement, $id, $name, $pwd);
                if(mysqli_stmt_fetch($statement)) {
                    if(password_verify($password, $pwd)) {
                        $_SESSION["customerid"] = $id;
                        $_SESSION["customername"] = $name;
                        header("Location: customer_home.php");
                    }
                    else {
                        $errormsg = "Invalid user.";
                    }
                }
                else {
                    $errormsg = "Wrong user.";
                }
            }
        }
    ?>
    <body>
        <div class="holder" ><br>
        <img src="miit_logo.png" style="margin-left: 100px;" width="100" height="100" class="center">
        <br>
        
        </div>
        <div class="menu">
            <a href="" style="margin-left: 0px;">Home</a>            
            <a href="" style="margin-left: 37px;">Contact</a>
            <a href="" style="margin-left: 37px;">About</a>
        </div>
        <div class="holder">
            <form name="frm" method="POST">
                <fieldset>
                    <legend>Log In</legend>
                    Email <input type="email" name="email" value="" required maxlength="45" title="Enter registered email to log in." autofocus /><br><br>
                    Password <input type="password" name="password" value="" required maxlength="20" title="Enter password to log in." /><br><br>
                    <input type="submit" value="Log In" name="login"  style="margin-right: 7px;" />
                    <input type="submit" value="Cancel" name="cancel" formnovalidate />
                    <label style="color: red;"><?php echo $errormsg; ?></label>
                </fieldset>
            </form>
        </div>
    </body>
</html>
