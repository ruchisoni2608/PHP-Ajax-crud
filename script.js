$(document).ready(function() {
    // Fetch and populate users table on page load
    fetchUsers();
  
    // Submit user form using Ajax
    $("#user-form").submit(function(event) {
      event.preventDefault();
      var formData = new FormData(this);
      var action = $("#user_id").val() !== '' ? 'update_user' : 'add_user';
      formData.append("action", action);
  
      $.ajax({
       
        url: "crud.php",
        type: "POST",
        data: formData,
        contentType: false,
        
        processData: false,
        success: function(response) {
          alert(response);
          // Clear the form fields after successful addition/update
          $("#user-form")[0].reset();
          // Refresh users table
          fetchUsers();
        }
      });
    });
  });
  
  
  // Function to fetch and populate users table
  function fetchUsers() {
    $.ajax({
      url: "crud.php",
      type: "POST",
      data: { action: "fetch_users" },
      dataType: "json",
      success: function(data) {
        var usersTable = $("#users-table tbody");
        usersTable.empty();
  
        if (data.length > 0) {
          $.each(data, function(index, user) {
            var row = $("<tr>");
            row.append("<td>" + user.name + "</td>");
            row.append("<td>" + user.email + "</td>");
            row.append("<td>" + user.gender + "</td>");
            row.append("<td>" + user.birthdate + "</td>");
            row.append("<td>" + user.country + "</td>");
           row.append("<td><img src='" + user.image + "' alt='User Image' height='50'></td>");
            row.append("<td>" + user.hobbies + "</td>");
            row.append("<td><button class='edit-btn' data-id='" + user.id + "'>Edit</button><button class='delete-btn' data-id='" + user.id + "'>Delete</button></td>");
  
            usersTable.append(row);
          });
        } else {
          usersTable.append("<tr><td colspan='8'>No users found.</td></tr>");
        }
         // Show the Add User button and hide the Update User button
      $("#update_user").hide();
      $("#add-user").show();
      }
    });
  }


// Edit user
$(document).on("click", ".edit-btn", function() {
  var user_id = $(this).data("id");
 // alert("rrr");
  $.ajax({
    url: "crud.php",
    type: "POST",
    data: { action: "get_user", user_id: user_id },
    dataType: "json",
    success: function(user) {
      $("#user_id").val(user.id);
      $("#name").val(user.name);
      $("#email").val(user.email);
      $("#gender").val(user.gender);
      $("#birthdate").val(user.birthdate);
      $("#country").val(user.country);
      $("#hobbies").val(user.hobbies);

      // Show the Update User button and hide the Add User button
      $("#add-user").hide();
      $("update_user").show();
    }
  });
});

// Update user
$("#update_user").click(function() {

  var formData = new FormData($("#user-form")[0]);
  formData.append("action", "update_user");
alert("ttt");
  $.ajax({
    url: "crud.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function(response) {
      alert(response);
      // Clear the form fields after successful update
      $("#user-form")[0].reset();
      // Refresh users table
      fetchUsers();

      // Show the Add User button and hide the Update User button
      $("#update_user").hide();
      $("#add-user").show();
    }
  });
});


  
  // Update user
$("#update_user").click(function() {
  var formData = new FormData($("#user-form")[0]);
  formData.append("action", "update_user");

  $.ajax({
      url: "crud.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
          alert(response);
          // Clear the form fields after successful update
          $("#user-form")[0].reset();
          // Refresh users table
          fetchUsers();
      }
  });
});

  // Delete user
  $(document).on("click", ".delete-btn", function() {
    var user_id = $(this).data("id");
    if (confirm("Are you sure you want to delete this user?")) {
      $.ajax({
        url: "crud.php",
        type: "POST",
        data: { action: "delete_user", user_id: user_id },
        success: function(response) {
          alert(response);
          // Refresh users table
          fetchUsers();
        }
      });
    }
  });
  