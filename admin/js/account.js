//function to delete the user
function editUser() {
  let userID = $("#user_Id").val();
  $.post("admin_functions.php", { userUpdateId: userID }, function (data) {
    const userData = JSON.parse(data);
    const imagePath = "../assets/picture/profiles/" + userData.ProfileImage;
    $("#updatePicture").attr("src", imagePath);
    $("#updatefirstName").val(userData.FirstName);
    $("#updatelastName").val(userData.LastName);
    $("#updatephoneNumber").val(userData.ContactNo);
    $("#updategender").val(userData.Gender);
  });
}

// function editUser() {
//   const UserID = $("#user_Id").val();
//   $.post("admin_functions.php", { fetchUserInfo: UserID }, function (data) {
//     const userData = JSON.parse(data);
//     if (userData.error) {
//       $(".accountMessage").html(
//         `<div class="alert alert-danger">${userData.error}</div>`
//       );
//       return;
//     }

//     const path = "../assets/picture/profiles/" + userData.ProfileImage;
//     $("#updatePicture").attr("src", path);
//     $("#updatefirstName").val(userData.FirstName);
//     $("#updatelastName").val(userData.LastName);
//     $("#updateemail").val(userData.Email);
//     $("#updatephoneNumber").val(userData.ContactNo);
//     $("#updategender").val(userData.Gender);
//   });
// }

$(document).ready(function () {
  editUser();

  //update the content of user
  $("#updateUser").on("submit", function (e) {
    e.preventDefault();

    var formData = new FormData(this);

    $.ajax({
      type: "POST",
      url: "admin_functions.php",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,

      success: function (data) {
        console.log("success");
        console.log(data);

        var json = JSON.parse(data);
        if (json["error"] != "") {
          editUser();
          $(".accountMessage").html(
            `<div class="alert alert-danger" role="alert"> ${json["error"]}  </div>`
          );
        } else {
          editUser();
          $(".accountMessage").html(
            `<div class="alert alert-success" role="alert"> ${json["msg"]}  </div>`
          );
        }
      },
      error: function (data) {
        console.log("error");
        console.log(data);
      },
    });
  });

  //update the password
  $("#change_password").on("submit", function (e) {
    e.preventDefault();

    var formData = new FormData(this);

    $.ajax({
      type: "POST",
      url: "admin_functions.php",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,

      success: function (data) {
        console.log("success");
        console.log(data);

        var json = JSON.parse(data);
        if (json["error"] != "") {
          editUser();
          $(".passwordMessage").html(
            `<div class="alert alert-danger" role="alert"> ${json["error"]}  </div>`
          );
        } else {
          editUser();
          $(".passwordMessage").html(
            `<div class="alert alert-success" role="alert"> ${json["msg"]}  </div>`
          );
        }
      },
      error: function (data) {
        console.log("error");
        console.log(data);
      },
    });
  });
});
