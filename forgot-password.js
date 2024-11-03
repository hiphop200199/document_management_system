$(function () {
  //ready function

  $("#forgot-form").on("submit", function (e) {
    e.preventDefault();
    $("#forgot-message").text("loading...");
    let formInputs = {
      email: $("#forgot-form input[type='email']").val(),
      task: "forgot-password",
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
        $("#forgot-message").text(response.message);
        console.log(response);
        if(response['redirect']){
            setTimeout(() => {
                location.href =
                location.origin + "/document_management_system/" + response.redirect;
            }, 1000);
        }
      
      },
      error: function (error) {
        $("#forgot-message").text("");
        console.log(error);
      },
    });
  });
});
