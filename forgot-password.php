<?php
 require_once "./parts/head.php";
 if($_GET) {
      header('Location:not-found.php');
}
?>
<div id="index">
<form id="forgot-form" class="form">    
      <input type="email" placeholder="email..." required>
      <p id="forgot-message" class="message"></p>     
         <button type="submit">send</button>
   </form>
</div>
<script src="forgot-password.js"></script>
<?php require_once './parts/foot.php';?>