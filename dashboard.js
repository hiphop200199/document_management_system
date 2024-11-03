$(function () {
  //ready function
  let PDFid;
  $("#reload").on("click", function () {
    location.reload();
  });

  $("#upload-dialog-btn").on("click", (e) => handleDialog(e, "upload", "open"));
  $("#delete-dialog-btn").on("click", (e) => handleDialog(e, "delete", "open"));
  $("#aside-upload-dialog-btn").on("click", (e) =>
    handleDialog(e, "upload", "open")
  );
  $("#aside-delete-dialog-btn").on("click", (e) =>
    handleDialog(e, "delete", "open")
  );
  $("#close-alert-modal").on("click", (e) => handleDialog(e, "alert", "close"));
  $("#close-upload-modal").on("click", (e) =>
    handleDialog(e, "upload", "close")
  );
  $("#close-delete-modal").on("click", (e) =>
    handleDialog(e, "delete", "close")
  );
  $("#confirm-delete").on("click", function (e) {
    e.preventDefault();
    $("#delete-message").text("請稍等..");
    let data = {
      task: "deleteItem",
      id: PDFid,
    };
    $.ajax({
      type: "POST",
      url: "file.php",
      data: JSON.stringify(data),
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (response) {
        $("#delete-message").text("刪除成功.");
        setTimeout(() => location.reload(), 1000);
        console.log(response);
      },
      error: function (error) {
        console.log(error);
      },
    });
  });

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

  $("#main-search-btn").on("click", (e) => handleSearch(e, "main"));
  $("#aside-search-btn").on("click", (e) => handleSearch(e, "aside"));

  $(".logout").on("click", function (e) {
    e.preventDefault();
    let formInputs = {
      task: "logout",
    };
    let data = JSON.stringify(formInputs);
    $.ajax({
      type: "POST",
      url: "auth.php",
      data: data,
      contentType: "application/json", //用來標示傳過去的資料格式
      processData: false,
      dataType: "json",
      success: function (response) {
        location.reload();
      },
      error: function (error) {
        console.log(error);
      },
    });
  });
  $("#upload-form").on("submit", function (e) {
    $("#upload-message").text("請稍等..");
    e.preventDefault();
    let files = document.getElementById("file").files;
    if (files.length === 0) {
      $("#upload-message").text("確認上傳該筆新文件?");
      handleDialog(e, "upload", "close");
      return;
    }
    let file = files[0];
    if (file.type !== "application/pdf") {
      $("#upload-message").text("請上傳pdf格式文件");
      return;
    }
    if (file.size > 5000000) {
      $("#upload-message").text("檔案太大");
      return;
    }
    let data = new FormData();
    data.append("file", file);
    $.ajax({
      type: "POST",
      url: "file.php",
      data: data,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (response) {
        $("#upload-message").text("上傳成功.");
        setTimeout(() => location.reload(), 1000);
        console.log(response);
      },
      error: function (error) {
        console.log(error);
      },
    });
  });

  function handleDialog(e, type, dir) {
    e.preventDefault();
    let object;
    switch (type) {
      case "login":
        object = "#login-dialog";
        break;
      case "alert":
        object = "#alert-dialog";
        break;
      case "upload":
        object = "#upload-dialog";
        break;
      case "delete":
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
    e.preventDefault();
    let object;
    type == "main" ? (object = "#main-") : (object = "#aside-");
    let keyword = $(object + "search-keyword").val();
    let startDate = $(object + "date-from").val();
    let endDate = $(object + "date-to").val();
    if (!(keyword || startDate)) {
      handleDialog(e, "alert", "open");
      return;
    } else {
      let formInputs = {
        keyword: keyword,
        startDate: startDate,
        endDate: endDate,
        task: "getItems",
      };
      let data = JSON.stringify(formInputs);
      $.ajax({
        type: "POST",
        url: "file.php",
        data: data,
        contentType: "application/json", //用來標示傳過去的資料格式
        processData: false,
        dataType: "json",
        success: function (response) {
          console.log(response);
          let list = document.getElementById("link-list");
          while (list.firstChild) {
            list.removeChild(list.firstChild);
          }
          clearPDF("pdf-container");
          $.each(response, function (i, elem) {
            let btn = document.createElement("button");
            btn.classList.add("link-btn");
            btn.innerText = elem["name"];
            btn.setAttribute("title", elem["name"]);
            btn.id = elem["id"];
            btn.onclick = function () {
              handlePDF(elem["source"], elem["name"], elem["id"]);
            };
            list.appendChild(btn);
          });
          showPDF(
            response[0]["source"],
            "pdf-container",
            "prev-page",
            "next-page",
            "page-indicator"
          );
          $("#delete-message").text("確認刪除" + response[0]["name"] + "?");
          PDFid = response[0]["id"];
        },
        error: function (error) {
          console.log(error);
        },
      });
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
      PDFViewer = pdf;

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
  function clearPDF(container) {
    if (PDFViewer) {
      PDFViewer.destroy();
      var element = document.getElementById(container);
      while (element.firstChild) {
        element.removeChild(element.firstChild);
      }
    }
  }
  $.ajax({
    type: "POST",
    url: "file.php",
    data: JSON.stringify({
      task: "getItems",
      keyword: "",
      startDate: "",
      endDate: "",
    }),
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (response) {
      console.log(response);
      $.each(response, function (i, elem) {
        let btn = document.createElement("button");
        btn.classList.add("link-btn");
        btn.innerText = elem["name"];
        btn.setAttribute("title", elem["name"]);
        btn.id = elem["id"];
        btn.onclick = function () {
          handlePDF(elem["source"], elem["name"], elem["id"]);
        };
        let list = document.getElementById("link-list");
        list.appendChild(btn);
      });
      showPDF(
        response[0]["source"],
        "pdf-container",
        "prev-page",
        "next-page",
        "page-indicator"
      );
      $("#delete-message").text("確認刪除" + response[0]["name"] + "?");
      PDFid = response[0]["id"];
    },
    error: function (error) {
      console.log(error);
    },
  });

  function handlePDF(src, name, id) {
    if (window.innerWidth <= 768) {
      $("#link-list").removeClass("active");
    }
    clearPDF("pdf-container");
    showPDF(src, "pdf-container", "prev-page", "next-page", "page-indicator");
    $("#delete-message").text("確認刪除" + name + "?");
    PDFid = id;
  }
});
