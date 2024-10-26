<?php require_once "./parts/head.php"; ?>
<h1 id="title">Management.</h1>
        <form id="register-form" class="form" action="auth.php" method="post">
            <input type="email" name="account" id="" placeholder="email..." required>
            <input type="password" name="password" id="" placeholder="password..." pattern="[A-Z]{1,}[a-z]{1,}[0-9]{1,}\W{1,}" minlength="8" title="須結合大小寫英文字母及數字以及特殊符號至少一個" required>
            <input type="hidden" name="type" value="register">
            <p class="message"></p>
            <section>
                <button type="submit">send</button>
            </section>
        </form>
<?php require_once './parts/foot.php';?>