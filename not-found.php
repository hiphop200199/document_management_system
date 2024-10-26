<?php
session_start();
require_once "./parts/head.php";
if(!isset(($_SESSION['user']))){ //沒權限就不給連
  header('Location:index.php');
}?>
<section id="operation">
  
  <form action="auth.php" method="post">
    <button type="submit">logout</button>
    <input type="hidden" name="type" value="logout">
  </form>
</section>

   <h1 id="title">404 Not Found.</h1>
   
 
      
  <?php require_once './parts/foot.php';?>