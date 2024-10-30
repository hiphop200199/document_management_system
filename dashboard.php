<?php require_once "./parts/head.php"; ?>
<div id="dashboard">
    <header>
        <button id="reload" title="reload">🗘</button>
        <section id="filter">
        <label for=""><input type="search" name="" id="" placeholder="請輸入關鍵字...">
        檔案類型：
            <select name="" id="">
                <option value="">*</option>
                <option value="">excel</option>
                <option value="">pdf</option>
                <option value="">word</option>
            </select>
        </label>
        <label>日期區間：<input type="date" name="" id="">~
        <input type="date" name="" id=""></label>
        </section>
        <section id="identity">
        <span>hello,!</span>
        <button>logout</button>
        </section>        
    </header>
    <section id="body">
    <aside>
        <button>ggg</button>
        <button>ggg</button>
        <button>ggg</button>
    </aside>
    <main>
    <object frameborder="0" style="width: 100%;height:100%" data="./uploads/PDF.pdf"  ></object>
    </main>
    </section>    
</div>
<?php require_once './parts/foot.php'; ?>