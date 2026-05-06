<?php

$conn = mysqli_connect('localhost', 'root','' , 'projector_system');

if (!$conn) {
    die("
        <h2 style='color:red; font-family:Arial; padding:20px;'>
            Database Connection Failed!
        </h2>
        <p style='font-family:Arial; padding:20px;'>
            Error: " . mysqli_connect_error() . "
            <br>Please check your settings inside db.php
        </p>
    ");
}
?>
