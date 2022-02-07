<?php
    $connection = mysqli_connect("localhost", "root", "", "testdb1");
    if (mysqli_connect_error()) {
        die(mysqli_connect_error());
    }