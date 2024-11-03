<?php
session_start();
require_once "./parts/head.php";
if (!isset(($_SESSION['user']))) { //沒權限就不給連
      header('Location:index.php');
}
if($_GET) {
      header('Location:not-found.php');
}
?>
<div id="index">
      <form id="reset-form" class="form">
            <input type="password" name="" id="" placeholder="new password..." pattern="[A-Z]{1,}[a-z]{1,}[0-9]{1,}\W{1,}" minlength="8" title="須結合大小寫英文字母及數字以及特殊符號至少一個" required>
            <p id="reset-message" class="message"></p>
            <button type="submit">send</button>
      </form>
</div>
<script src="reset-password.js"></script>
<?php require_once './parts/foot.php'; ?>