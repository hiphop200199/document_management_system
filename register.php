<?php
 require_once "./parts/head.php";
 if($_GET) {
      header('Location:not-found.php');
}
?>
<div id="index">
<form id="register-form" class="form">    
      <input type="text" name="" id="" placeholder="name..." required>
      <input type="email" placeholder="email..." required>
      <input type="password" name="" id="" placeholder="password..." pattern="[A-Z]{1,}[a-z]{1,}[0-9]{1,}\W{1,}" minlength="8" title="須結合大小寫英文字母及數字以及特殊符號至少一個" required>
      <p id="register-message" class="message"></p>     
         <button type="submit">send</button>
   </form>
</div>
<script src="register.js"></script>
<?php require_once './parts/foot.php';?>