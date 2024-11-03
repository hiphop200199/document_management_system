<?php
session_start();
require_once "./parts/head.php";
if(!isset(($_SESSION['user']))){ //沒權限就不給連
  header('Location:index.php');
}
if($_GET) {
    header('Location:not-found.php');
}
?>
<div id="dashboard">
    <header>
        <button id="reload" title="reload">🗘</button>
        <section id="filter">

            <form>
                <span> <input type="search" name="" id="main-search-keyword" placeholder="請輸入關鍵字...">
                    <button id="main-search-btn" title="搜尋"><img src="./assets/search.png" alt=""></button></span>
                <label>日期區間：<input type="date" name="" id="main-date-from">~
                    <input type="date" name="" id="main-date-to"></label>
            </form>
        </section>
        <section id="identity">
            <span>hello,<?=$_SESSION['user']?>!</span>
            <?php if(isset($_SESSION['identity'])&&$_SESSION['identity']==='admin') : ?>
            <section id="admin-btns">
                <button id="upload-dialog-btn" title="上傳文件">⭱</button>
                <button id="delete-dialog-btn" title="刪除文件">🗑</button>
            </section>
            <?php endif ?>
            <button id="logout" class="logout">logout</button>
        </section>
        <button id="aside-menu-btn">☰</button>
    </header>
    <section id="body">
        <aside id="link-list">
        </aside>
        <aside id="aside-menu">
            <span>hello,<?=$_SESSION['user']?>!</span>
            <button id="link-list-btn">查詢結果</button>
            <form>
                <span> <input type="search" name="" id="" placeholder="請輸入關鍵字...">
                    <button id="aside-search-btn" title="搜尋"><img src="./assets/search.png" alt=""></button></span>
                <label>日期區間：<br><input type="date" name="" id="">~<br>
                    <input type="date" name="" id=""></label>
            </form>
            <?php if(isset($_SESSION['identity'])&&$_SESSION['identity']==='admin') : ?>
            <button id="aside-upload-dialog-btn">上傳文件</button>
            <button id="aside-delete-dialog-btn">刪除文件</button>
            <?php endif ?>
            <button id="aside-logout" class="logout">logout</button>
        </aside>
        <main>
            <div id="controls">
                <button id="prev-page" title="上一頁">Previous</button>
                <span id="page-indicator"></span>
                <button id="next-page" title="下一頁">Next</button>
            </div>
            <div id="pdf-container"></div>
        </main>
    </section>
</div>
<dialog id="alert-dialog">
    <button id="close-alert-modal">✖</button>
    <p id="alert">請輸入關鍵字或日期!</p>
</dialog>
<?php  if(isset($_SESSION['identity'])&&$_SESSION['identity']==='admin') : ?>
<dialog id="upload-dialog">
<button id="close-upload-modal">✖</button>
<form id="upload-form" method="post" enctype="multipart/form-data">
<input type="file" name="file" id="file" accept="application/pdf">
<p id="upload-message">確認上傳該筆新文件?</p>
<button type="submit" id="confirm-upload">上傳</button>
</form>
</dialog>
<?php endif ?>
<?php  if(isset($_SESSION['identity'])&&$_SESSION['identity']==='admin') : ?>
<dialog id="delete-dialog">
<button id="close-delete-modal">✖</button>
<p id="delete-message">確認刪除該筆資料?</p>
<button id="confirm-delete">刪除</button>
</dialog>
<?php endif ?>
<script src="dashboard.js"></script>
<?php require_once './parts/foot.php'; ?>