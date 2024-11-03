$(function () {
  //ready function

  $("#register-form").on("submit", function (e) {
    e.preventDefault();
    $("#register-message").text("loading...");
    let formInputs = {
      name: $("#register-form input[type='text']").val(),
      email: $("#register-form input[type='email']").val(),
      password: $("#register-form input[type='password']").val(),
      task: "register",
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
        $("#register-message").text(response.message);
        console.log(response);
        if(response['redirect']){
            setTimeout(() => {
                location.href =
                location.origin + "/document_management_system/" + response.redirect;
            }, 1000);
        }
       
      
      },
      error: function (error) {
        $("#register-message").text("");
        console.log(error);
      },
    });
  });
});
