<?php

include("../include/functions.php");
if(isset($_POST['bookRoom'])){

    $roomTypeId = $_POST['roomTypeId'];
    $email = $_POST['email'];
    $contactno = $_POST['contactno'];
    $no_of_guest = $_POST['no_of_guest'];
    $checkIn = $_POST['checkIn'];
    $checkOut = $_POST['checkOut'];
    $totalCost = $_POST['totalCost'];
    $userId = $_SESSION['loggedUserId'];

    $checkIn = strtotime($checkIn);
    $checkIn = date('Y-m-d',$checkIn); 
    
    $checkOut = strtotime($checkOut);
    $checkOut = date('Y-m-d',$checkOut);

    $query_roomType = "select * from room_list where RoomTypeId = '$roomTypeId' AND Status = 'active' order by RoomId";
    $roomType  = mysqli_query($con,$query_roomType);
    if(mysqli_num_rows($roomType)>0)
{  
  while($row=mysqli_fetch_assoc($roomType))
{
      $flag = false;
      $ID=$row['RoomId'];
      if ($row['Booking_status']=='Available')
      { 
           $flag =true;
           $reg="INSERT into room_booking (RoomId,User_id,Date,CheckIn,CheckOut,NoOfGuest,Amount,Email,Phone_number)
            values('$ID','$userId',curdate(),'$checkIn','$checkOut','$no_of_guest','$totalCost','$email','$contactno') ";

           $update_query = "UPDATE room_list SET Booking_status = 'Booked' where RoomId = '$ID'";
            
           mysqli_query($con,$update_query);
           mysqli_query($con,$reg);
           break ; 
      }
      
  }
    if ($flag==false)
    {
       
        echo "<script>alert('Oops! Rooms are not available..'); window.location.href='room.php'; </script>";
        
    }
      else {
 
        echo "<script>alert('Booking Successfull..'); window.location.href='mybooking.php'; </script>";
}
     
}
else {
    echo "<script>alert('Oops! Rooms are not available'); window.location.href='room.php'; </script>";
}


}

if(isset($_POST['bookEvent'])){

    $eventTypeId = $_POST['eventTypeId'];
    $email = $_POST['email'];
    $contactno = $_POST['contactno'];
    $no_of_guest = $_POST['no_of_guest'];
    $eventDate = $_POST['eventDate'];
    $eventTime = $_POST['eventTime'];
    $eventTime = date("H:i", strtotime($eventTime));
    $total_hours = $_POST['total_hours'];
    $totalCost = $_POST['totalCost'];
    $userId = $_SESSION['loggedUserId'];

    $eventDate = strtotime($eventDate);
    $eventDate = date('Y-m-d',$eventDate); 
    

    $query_eventType = "select * from event_list where EventTypeId = '$eventTypeId' AND Status = 'active' order by EventId";
    $Type  = mysqli_query($con,$query_eventType);
    if(mysqli_num_rows($Type)>0)
{  
  while($row=mysqli_fetch_assoc($Type))
{
      $flag = false;
      $ID=$row['EventId'];
      if ($row['Booking_status']=='Available')
      { 
          
           $reg="INSERT into event_booking (EventId,User_id,Date,Event_date,NoOfGuest,EventTime,Package,Amount,Email,Phone_number)
            values('$ID','$userId',curdate(),'$eventDate','$no_of_guest','$eventTime','$total_hours','$totalCost','$email','$contactno') ";

           $update_query = "UPDATE event_list SET Booking_status = 'Booked' where EventId = '$ID'";
           
           if(mysqli_query($con,$update_query) &&  mysqli_query($con,$reg)){
             $flag =true;
           }
           break ; 
      }
      
  }
    if ($flag==false)
    {
       
        echo "<script>alert('Oops! Event hall are not available..'); window.location.href='event.php'; </script>";
        
    }
      else {
 
        echo "<script>alert('Booking Successfull..');window.location.href='mybooking.php'; </script>";

}
     
}
else {
    echo "<script>alert('Oops! Active Event halls are not available'); window.location.href='room.php'; </script>";
}


}

// ----------------------------------------- Account Action -----------------------------------------------
//update the datals of user table

if (isset($_POST['updateAccount'])) {
  $user_id = mysqli_real_escape_string($con, $_POST['updateAccount']);
  $firstname = trim(mysqli_real_escape_string($con, $_POST['firstName']));
  $lastname = trim(mysqli_real_escape_string($con, $_POST['lastname']));
  $email = trim(mysqli_real_escape_string($con, $_POST['email']));
  $contactno = trim(mysqli_real_escape_string($con, $_POST['contactno']));
  $gender = trim(mysqli_real_escape_string($con, $_POST['gender']));
  
  $profileImageName = $_FILES["profileImage"]["name"];
  $tempname = $_FILES["profileImage"]["tmp_name"];
  $folder = "../assets/picture/profiles/" . $profileImageName;

  $sendData = array();

  // Validation rules
  if ($firstname === "admin") {
      $sendData = ["msg" => "", "error" => "Invalid name. You can't use 'admin' as your first name."];
  } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $firstname) || !preg_match("/^[a-zA-Z-' ]*$/", $lastname)) {
      $sendData = ["msg" => "", "error" => "Name can only contain letters, spaces and hyphens."];
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $sendData = ["msg" => "", "error" => "Invalid email format."];
  } elseif (!preg_match("/^\+[0-9]{10,15}$/", $contactno)) {
      $sendData = ["msg" => "", "error" => "Phone number must start with '+' and be 10–15 digits long."];
  } elseif (!in_array($gender, ["male", "female"])) {
      $sendData = ["msg" => "", "error" => "Please select a valid gender."];
  } else {
      // Check for duplicates excluding the current user
      $checkQuery = "SELECT * FROM users_details WHERE (FirstName='$firstname' OR Email='$email') AND UserId <> '$user_id'";
      $result = mysqli_query($con, $checkQuery);
      if (mysqli_num_rows($result) > 0) {
          $sendData = ["msg" => "", "error" => "First name or email is already in use by another user."];
      } else {
          // Perform update
          $update = "UPDATE users_details SET 
                      FirstName='$firstname', 
                      LastName='$lastname', 
                      Email='$email', 
                      ContactNo='$contactno', 
                      Gender='$gender', 
                      ProfileImage='$profileImageName' 
                     WHERE UserId='$user_id'";
          
          if (mysqli_query($con, $update)) {
              if ($profileImageName != "" && !move_uploaded_file($tempname, $folder)) {
                  $sendData = ["msg" => "", "error" => "Profile updated but image upload failed."];
              } else {
                  $sendData = ["msg" => "Profile updated successfully!", "error" => ""];
              }
          } else {
              $sendData = ["msg" => "", "error" => "Something went wrong while updating. Try again later."];
          }
      }
  }

  echo json_encode($sendData);
}


// -------------------------------- Change password -----------------------------------

if (isset($_POST["oldPassword"])) {
  $old = trim($_POST['oldPassword']);
  $new = trim($_POST['newPassword']);
  $ID = $_POST['change_password'];

  $sendData = [];

  // Fetch user
  $Q = "SELECT * FROM users_details WHERE UserId = '$ID'";
  $res = mysqli_query($con, $Q);
  $row = mysqli_fetch_assoc($res);
  $num = mysqli_num_rows($res);

  if ($num > 0) {
      // Verify old password using password_verify
      if (password_verify($old, $row['Password'])) {
          // Validate new password strength
          $number = preg_match('@[0-9]@', $new);
          $uppercase = preg_match('@[A-Z]@', $new);
          $lowercase = preg_match('@[a-z]@', $new);
          $specialChars = preg_match('@[^\w]@', $new);

          if (strlen($new) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
              $sendData = [
                  "msg" => "",
                  "error" => "New password must be at least 8 characters and include a number, uppercase, lowercase, and special character."
              ];
          } else {
              // Hash the new password
              $hashedPassword = password_hash($new, PASSWORD_DEFAULT);

              // Update password
              $Q_update = "UPDATE users_details SET Password = '$hashedPassword' WHERE UserId = '$ID'";
              if (mysqli_query($con, $Q_update)) {
                  $sendData = ["msg" => "Password changed successfully!", "error" => ""];
              } else {
                  $sendData = ["msg" => "", "error" => "Error updating password. Please try again."];
              }
          }
      } else {
          $sendData = ["msg" => "", "error" => "Incorrect old password."];
      }
  } else {
      $sendData = ["msg" => "", "error" => "Invalid user ID."];
  }

  echo json_encode($sendData);
}

?>