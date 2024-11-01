<?php require_once "./parts/head.php"; ?>
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
            <span>hello,!</span>
            <section id="admin-btns"><button id="upload-dialog-btn">⭱</button>
            <button id="delete-dialog-btn">🗑</button></section>
            <button id="logout">logout</button>
        </section>
        <button id="aside-menu-btn">☰</button>
    </header>
    <section id="body">
        <aside id="link-list">
            <button class="link-btn">ggg</button>
            <button class="link-btn">ggg</button>
            <button class="link-btn">ggg</button>
            <button class="link-btn">ggg</button>

        </aside>
        <aside id="aside-menu">
            <span>hello,!</span>
            <button id="link-list-btn">查詢結果</button>
            <form>
                <span> <input type="search" name="" id="" placeholder="請輸入關鍵字...">
                    <button id="aside-search-btn" title="搜尋"><img src="./assets/search.png" alt=""></button></span>
                <label>日期區間：<br><input type="date" name="" id="">~<br>
                    <input type="date" name="" id=""></label>
            </form>
            <button id="aside-upload-dialog-btn">上傳文件</button>
            <button id="aside-delete-dialog-btn">刪除文件</button>
            <button>logout</button>
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
<dialog id="upload-dialog">
<button id="close-upload-modal">✖</button>
<input type="file" name="" id="file">
<p id="upload-message">確認上傳該筆新文件?</p>
<button id="confirm-upload">上傳</button>

</dialog>
<dialog id="delete-dialog">
<button id="close-delete-modal">✖</button>
<p id="delete-message">確認刪除該筆資料?</p>
<button id="confirm-delete">刪除</button>
</dialog>
<?php require_once './parts/foot.php'; ?>