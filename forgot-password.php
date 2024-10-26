<?php require_once "./parts/head.php"; ?>
<h1 id="title">Management.</h1>
        <form id="forgot-password-form" class="form" action="auth.php" method="post">
            <input type="email" name="account" id="" placeholder="email..." required>
            <input type="hidden" name="type" value="forgot-password">
            <p class="message"></p>
            <section>
                <button type="submit">send</button>
            </section>
        </form>
<?php require_once './parts/foot.php';?>