$(function () {
  //ready function

  $("#reset-form").on("submit", function (e) {
    e.preventDefault();
    $("#reset-message").text("loading...");
    let formInputs = {
      password: $("#reset-form input[type='password']").val(),
      task: "reset-password",
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
        $("#reset-message").text(response.message);
        console.log(response);
        if(response['redirect']){
            setTimeout(() => {
                location.href =
                location.origin + "/document_management_system/" + response.redirect;
            }, 1000);
        }
       
      },
      error: function (error) {
        $("#reset-message").text("");
        console.log(error);
      },
    });
  });
});
