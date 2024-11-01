$(function () {
  //ready function

  $("#reload").on("click", function () {
    location.reload();
  });

  $("#login").on("click", (e) => handleDialog(e, "login", "open"));
  $("#upload-dialog-btn").on("click", (e) => handleDialog(e, "upload", "open"));
  $("#delete-dialog-btn").on("click", (e) => handleDialog(e, "delete", "open"));
  $("#aside-upload-dialog-btn").on("click", (e) => handleDialog(e, "upload", "open"));
  $("#aside-delete-dialog-btn").on("click", (e) => handleDialog(e, "delete", "open"));
  $("#close-login-modal").on("click", (e) => handleDialog(e, "login", "close"));
  $("#close-alert-modal").on("click", (e) =>handleDialog(e, "alert", "close"));
$('#close-upload-modal').on('click',e => handleDialog(e,'upload','close'));
$('#close-delete-modal').on('click',e => handleDialog(e,'delete','close'));


  $("#aside-menu-btn").on("click", function () {
    $(this).toggleClass("active");
    $("#aside-menu").toggleClass("active");
    $("#link-list").removeClass("active");
  });

  $("#link-list-btn").on("click", function () {
    $("#aside-menu-btn").removeClass("active");
    $("#aside-menu").removeClass("active");
    $("#link-list").addClass("active");
  });

  $(".link-btn").on("click", function () {
    if (window.innerWidth <= 768) {
      $("#link-list").removeClass("active");
    }
  });

  $("#main-search-btn").on("click", (e) => handleSearch(e, "main"));
  $("#aside-search-btn").on("click", (e) => handleSearch(e, "aside"));


  $('#register-form').on('submit',function(e){
      e.preventDefault()
      let formInputs = {
        name:$("#register-form input[type='text']").val(),
        email:$("#register-form input[type='email']").val(),
        password:$("#register-form input[type='password']").val(),
      }
      let data = JSON.stringify(formInputs)
      $.ajax({
        type: "POST",
        url: "auth.php",
        data: data,
        contentType: "application/json",//用來標示傳過去的資料格式
        processData:false,
        dataType: "json",
        success: function (response) {
          console.log(response);          
        },
        error:function (error){
          console.log(error);                
      }
      });
  })

  function handleDialog(e, type, dir) {
    e.preventDefault();
    let object;
    switch(type){
      case 'login':
        object = "#login-dialog";
        break;
      case 'alert':
        object = "#alert-dialog";
        break;
        case 'upload':
          object = "#upload-dialog";
          break;
          case 'delete':
            object = "#delete-dialog";
            break;    
    }
   
    if (dir == "open") {
      document.querySelector(object).showModal();
      document.querySelector(object).classList.add("open");
    } else {
      document.querySelector(object).classList.remove("open");
      setTimeout(() => document.querySelector(object).close(), 700);
    }
  }
  function handleSearch(e, type) {
    let object;
    type == "main" ? (object = "#main-") : (object = "#aside-");
    let keyword = $(object + "search-keyword").val();
    let startDate = $(object + "date-from").val();
    let endDate = $(object + "date-to").val();
    if (!keyword && !startDate && !endDate) {
      handleDialog(e, "alert", "open");
    }
  }

  function showPDF(pdfUrl, containerId, prevBtnId, nextBtnId, pageIndicatorId) {
    //抓取pdf容器,上一頁,下一頁,所在頁數指示等元素參考，並設定啟始頁為第一頁
    const container = document.getElementById(containerId);
    const prevPageBtn = document.getElementById(prevBtnId);
    const nextPageBtn = document.getElementById(nextBtnId);
    const pageIndicator = document.getElementById(pageIndicatorId);
    let currentPage = 1;

    //建立canvas畫布並附著於pdf容器上
    const canvas = document.createElement("canvas");
    container.appendChild(canvas);

    //進行pdf文件載入
    const loadingTask = pdfjsLib.getDocument(pdfUrl);
    loadingTask.promise.then((pdf) => {
      const numPages = pdf.numPages;
      const PDFViewer = pdf;

      displayPage(PDFViewer, currentPage);

      prevPageBtn.addEventListener("click", () => {
        if (currentPage > 1) {
          currentPage--;
          displayPage(PDFViewer, currentPage);
        }
      });
      nextPageBtn.addEventListener("click", () => {
        if (currentPage < numPages) {
          currentPage++;
          displayPage(PDFViewer, currentPage);
        }
      });

      function displayPage(PDFViewer, pageNumber) {
        // 以當下頁數判斷是否讓按鈕失效
        prevPageBtn.disabled = pageNumber === 1;
        nextPageBtn.disabled = pageNumber === numPages;

        pageIndicator.textContent = `${pageNumber} / ${numPages}`;

        //取得指定頁數並準備渲染該畫面
        PDFViewer.getPage(pageNumber).then((page) => {
          var desiredScale = 1;

          // 以pdf文件原始的尺寸倍數進行渲染
          var viewport = page.getViewport({ scale: desiredScale });
          canvas.width = viewport.width;
          canvas.height = viewport.height;
          canvas.style.width = "100%";
          canvas.style.height = "100%";

          var renderContext = {
            canvasContext: canvas.getContext("2d"),
            viewport: viewport,
          };

          var renderTask = page.render(renderContext);
          renderTask.promise.then((task) => {});
        });
      }
    });
  }

  showPDF(
    "uploads/PDF.pdf",
    "pdf-container",
    "prev-page",
    "next-page",
    "page-indicator"
  );
});
