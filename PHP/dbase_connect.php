<?php
   // Database connection
   $server = 'sql311.byethost3.com'; 
   $user = 'b3_40935809'; 
   $password = 'CVpYA90hKZapo7';
   $database = 'b3_40935809_brightminds';

   $conn = mysqli_connect($server, $user, $password, $database); 

   if (!$conn) { 
       die('Database Connection failed: ' . mysqli_connect_error()); 
   }
?>