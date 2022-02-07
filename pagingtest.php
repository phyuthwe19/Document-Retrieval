<html>
   
   <head>
      <title>Paging Using PHP</title>
   </head>
   
   <body>
      <?php
        require_once './mysqli_connection.php';
         //$dbhost = "localhost:3306";
         //$dbuser = "root";
         //$dbpass = "";
         
         //$rec_limit = 10;
         //$conn = mysql_connect($dbhost, $dbuser, $dbpass);
         
         //if(! $conn ) {
         //   die('Could not connect: ' . mysql_error());
         //}
         //mysql_select_db('testdb1');
         
         /* Get total number of records */
        
        
            
            $id = 352;
            $statement = mysqli_prepare($connection, "SELECT count(out_id) FROM outgoing_message WHERE out_id = ?");
            mysqli_stmt_bind_param($statement, "i", $id);
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $countID);
            if(mysqli_stmt_fetch($statement)) {
                
            }
            
        
        
         
         
         if(! $retval ) {
            die('Could not get data: ' . mysql_error());
         }
         $row = mysql_fetch_array($retval, MYSQL_NUM );
         $rec_count = $row[0];
         
         if( isset($_GET{'page'} ) ) {
            $page = $_GET{'page'} + 1;
            $offset = $rec_limit * $page ;
         }else {
            $page = 0;
            $offset = 0;
         }
         
         $left_rec = $rec_count - ($page * $rec_limit);
         $sql = "SELECT out_id, letter_no, description ". 
            "FROM outgoing_message ";
            
         $retval = mysql_query( $sql, $conn );
         
         if(! $retval ) {
            die('Could not get data: ' . mysql_error());
         }
         
         while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            echo "OUT ID :{$row['out_id']}  <br> ".
               "LETTER NO : {$row['letter_no']} <br> ".
               "DESCRIPTION : {$row['description']} <br> ".
               "--------------------------------<br>";
         }
         
         if( $page > 0 ) {
            $last = $page - 2;
            echo "<a href = \"$_PHP_SELF?page = $last\">Last 10 Records</a> |";
            echo "<a href = \"$_PHP_SELF?page = $page\">Next 10 Records</a>";
         }else if( $page == 0 ) {
            echo "<a href = \"$_PHP_SELF?page = $page\">Next 10 Records</a>";
         }else if( $left_rec < $rec_limit ) {
            $last = $page - 2;
            echo "<a href = \"$_PHP_SELF?page = $last\">Last 10 Records</a>";
         }
         
         mysql_close($conn);
      ?>
      
   </body>
</html>
