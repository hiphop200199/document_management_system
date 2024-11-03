$(function () {
  //ready function

  $("#login").on("click", (e) => handleDialog(e, "login", "open"));

  $("#close-login-modal").on("click", (e) => handleDialog(e, "login", "close"));

  $("#login-form").on("submit", function (e) {
    e.preventDefault();
    $("#login-message").text("loading...");
    let formInputs = {
      email: $("#login-form input[type='email']").val(),
      password: $("#login-form input[type='password']").val(),
      task: "login",
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
        $("#login-message").text(response.message);
        console.log(response);
        if(response['redirect']){
          setTimeout(() => {
            location.href =
            location.origin + "/document_management_system/" + response.redirect;
        }, 1000);
        }
       
      },
      error: function (error) {
        $("#login-message").text("");
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
});
