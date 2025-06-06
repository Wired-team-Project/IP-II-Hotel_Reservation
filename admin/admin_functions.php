
<?php

include('../include/functions.php');
// ---------------------------------------User Actions----------------------------------------------

if (isset($_POST['firstname'])) {

    $firstname = trim(ucfirst(strtolower(mysqli_real_escape_string($con, $_POST['firstname']))));
    $lastname = trim(ucfirst(strtolower(mysqli_real_escape_string($con, $_POST['lastname']))));
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contactno = mysqli_real_escape_string($con, $_POST['contactno']);  
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($con, $_POST['conformPassword']);

    $profileImageName = $_FILES["profileImage"]["name"];
    $tempname = $_FILES["profileImage"]["tmp_name"];   
    $folder = "../assets/picture/profiles/" . $profileImageName;
    $imageFileType = strtolower(pathinfo($profileImageName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    $errors = [];

    // Validate names
    if (!preg_match("/^[a-zA-Z]{2,}$/", $firstname)) {
        $errors[] = "First name must contain only letters and be at least 2 characters.";
    }

    elseif (!preg_match("/^[a-zA-Z]{2,}$/", $lastname)) {
        $errors[] = "Last name must contain only letters and be at least 2 characters.";
    }

    // Email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Phone number
    elseif (!preg_match("/^\+[0-9]{10,15}$/", $contactno)) {
        $errors[] = "Phone number must be in international format (e.g., +251912345678).";
    }

    // Role validation
    $validRoles = ['client', 'receptionist', 'admin'];
    if (!in_array(strtolower($role), $validRoles)) {
        $errors[] = "Invalid role selected.";
    }

    // Image validation
    elseif (!empty($profileImageName) && !in_array($imageFileType, $allowedExtensions)) {
        $errors[] = "Only JPG, JPEG, PNG, or WEBP image formats are allowed.";
    }

    // Password validation
    elseif (strlen($password) < 8 ||
        !preg_match('@[0-9]@', $password) ||
        !preg_match('@[A-Z]@', $password) ||
        !preg_match('@[a-z]@', $password) ||
        !preg_match('@[^\w]@', $password)) {
        $errors[] = "Password must be at least 8 characters and contain uppercase, lowercase, number, and special character.";
    }

    elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check for existing user
    $checkQuery = "SELECT * FROM users_details WHERE FirstName = '$firstname' OR Email = '$email'";
    $checkResult = mysqli_query($con, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        $errors[] = "First name or email already exists.";
    }

    elseif ($firstname == "admin") {
        $errors[] = "Invalid First Name (you cannot use 'admin' as name)";
    }

    // Return if errors
    elseif (!empty($errors)) {
        echo json_encode(["msg" => "Succesfully Added", "error" => implode("<br>", $errors)]);
        exit;
    }else{
            // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $insert = "INSERT INTO users_details 
                (FirstName, LastName, Email, Password, ContactNo, Gender, ProfileImage, Role) 
               VALUES 
                ('$firstname', '$lastname', '$email', '$hashedPassword', '$contactno', '$gender', '$profileImageName', '$role')";
    
    if (mysqli_query($con, $insert)) {
            echo json_encode(["msg" => "User Added Successfully", "error" => ""]);
        
    } else {
        echo json_encode(["msg" => "", "error" => "Error inserting into database. Try again later."]);
    }
    }

}

// delete of user account
if(isset($_POST['deleteUser'])){
    $user_id = mysqli_real_escape_string($con, $_POST['userId']);
    $query_deleteUser = "Delete from users_details where UserId = '$user_id' ";
    $sendData = array();
    if(mysqli_query($con,$query_deleteUser)){
        $sendData = array(
            "msg"=>"User is Deleted",
            "error"=>""
        );
        echo json_encode($sendData);
    }else{
        $error = "User Has a booking";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );
        echo json_encode($sendData);
    }
}

//update - getting the selected user details
if(isset($_POST['userUpdateId'])){
    $userID = $_POST['userUpdateId'];
   
    $query_selectUser = "select * from users_details where UserId = '$userID' ";
    $single_user = mysqli_query($con,$query_selectUser);
    $num_of_rows = mysqli_num_rows($single_user);
    $response = array();
    if($num_of_rows<1){
        $response['status'] = 200;
        $response['message'] = "invalid user id";
    }else{
        while($row = mysqli_fetch_assoc($single_user)){
            $response = $row;
        }
    }
    echo json_encode($response);
}


//update the datals of user table

if (isset($_POST['updateUserID'])) {
    $user_id = trim(mysqli_real_escape_string($con, $_POST['updateUserID']));
    $firstname = trim(mysqli_real_escape_string($con, $_POST['firstName']));
    $lastname = trim(mysqli_real_escape_string($con, $_POST['lastname']));
    $email = trim(mysqli_real_escape_string($con, $_POST['email']));
    $contactno = trim(mysqli_real_escape_string($con, $_POST['contactno']));
    $gender = trim(mysqli_real_escape_string($con, $_POST['gender']));
    $status = trim(mysqli_real_escape_string($con, $_POST['status']));

    $sendData = [];

    // Optional: Handle file upload if new image selected
    $profileImageName = $_FILES["profileImage"]["name"];
    $tempname = $_FILES["profileImage"]["tmp_name"];
    $folder = "../assets/picture/profiles/" . $profileImageName;

    // --- Validations ---
    if ($firstname === "admin") {
        $sendData = ["msg" => "", "error" => "Invalid name. You can't use 'admin' as a first name."];
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $firstname) || !preg_match("/^[a-zA-Z-' ]*$/", $lastname)) {
        $sendData = ["msg" => "", "error" => "Name can only contain letters, spaces and hyphens."];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sendData = ["msg" => "", "error" => "Invalid email format."];
    } elseif (!preg_match("/^\+[0-9]{10,15}$/", $contactno)) {
        $sendData = ["msg" => "", "error" => "Phone number must be in international format, e.g., +2519XXXXXXXX."];
    } elseif (!in_array($gender, ['male', 'female'])) {
        $sendData = ["msg" => "", "error" => "Invalid gender selection."];
    } else {

            // Final update query
            $updateQuery = "UPDATE users_details SET 
                FirstName = '$firstname',
                LastName = '$lastname',
                Email = '$email',
                ContactNo = '$contactno',
                Gender = '$gender',
                Status = '$status'";

            if (!empty($profileImageName)) {
                $updateQuery .= ", ProfileImage = '$profileImageName'";
            }

            $updateQuery .= " WHERE UserId = '$user_id'";

            if (mysqli_query($con, $updateQuery)) {
                if (!empty($profileImageName)) {
                    move_uploaded_file($tempname, $folder);
                }
                $sendData = ["msg" => "User details updated successfully!", "error" => ""];
            } else {
                $sendData = ["msg" => "", "error" => "Error updating user. Try again later."];
            }
    }

    echo json_encode($sendData);
}


// ------------------------------------- Gallery Actions -----------------------------------------------------

if(isset($_FILES['galleryImage'])){
 
        $profileImageName = $_FILES["galleryImage"]["name"];
        $tempname = $_FILES["galleryImage"]["tmp_name"];   
        $folder = "../assets/picture/gallery/".$profileImageName;
        $sendData = array();

       if(!move_uploaded_file($tempname, $folder)){
        
            $error ="Error in Adding ...! Try after sometime";
            $sendData = array(
                "msg"=>"",
                "error"=>$error
            );
           
        }
        else{

          $message = "Image is Added";
            $sendData = array(
                "msg"=>$message,
                "error"=>""
            );
       
        }

        echo json_encode($sendData);
}


if(isset($_POST['deleteImage'])){

$filename = $_POST['Url'];
$sendData = array();
if (unlink($filename)) {
	$sendData = array(
        "msg"=>"Image is Deleted",
        "error"=>""
    );
} else {
    $sendData = array(
        "msg"=>"",
        "error"=>"Error in Deleting Images.."
    );
}
echo json_encode($sendData);
}

// --------------------------------------- Room Type Action ----------------------------------------

//add new type of room
if(isset($_POST['roomTypeName'])){

    $roomType = ucfirst($_POST['roomTypeName']);
    $roomCost =  $_POST['roomCost'];
    $desc =  $_POST['description'];
    $fileName = $_FILES['roomTypeImage']['name'];
    $tempname = $_FILES['roomTypeImage']['tmp_name'];
    $folder = "../assets/picture/RoomType/".$fileName;

    $sql_roomType = "select * from room_type where roomType like '$roomType'";
    $result_room = mysqli_query($con,$sql_roomType);
    $num_rooms = mysqli_num_rows($result_room);

    $sendData = array();
    if($num_rooms>=1){
        $sendData = array(
            "msg" => "",
            "error"  => "Room Type is already Exist"
        );
    }
    else{
       $insert_query = "insert into room_type(RoomType,RoomImage,Cost,Description) values('$roomType','$fileName','$roomCost','$desc')";
       
       if(mysqli_query($con,$insert_query)){

        if(!move_uploaded_file($tempname, $folder)){
        
            $error ="Error in Adding ...! Try after sometime";
            $sendData = array(
                "msg"=>"",
                "error"=>$error
            );
           
        }
        else{
    
          $message = "Room Type is Added";
            $sendData = array(
                "msg"=>$message,
                "error"=>""
            );
       
        }
       }
       else{

        $error ="Error in Adding ...! Try after sometime";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );

       }
    }
    echo json_encode($sendData);
}

// deleting the room type

if(isset($_POST['deleteRoomType'])){
    $type_id = $_POST['typeId'];
    $query_deleteUser = "Delete from room_type where RoomTypeId = '$type_id' ";
    $sendData = array();
    if(mysqli_query($con,$query_deleteUser)){
        $sendData = array(
            "msg"=>"This Room Type is Deleted",
            "error"=>""
        );
        echo json_encode($sendData);
    }else{
        $error = "This Room Type Has a Rooms in Hotel";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );
        echo json_encode($sendData);
    }
}


//update - getting the selected room type details

if(isset($_POST['roomTypeUpdateId'])){
    $roomTypeUpdateId = $_POST['roomTypeUpdateId'];
   
    $query_type = "select * from room_type where RoomTypeId = '$roomTypeUpdateId' ";
    $single_type = mysqli_query($con,$query_type);
    $num_of_rows = mysqli_num_rows($single_type);
    $response = array();
    if($num_of_rows<1){
        $response['status'] = 200;
        $response['message'] = "invalid user id";
    }else{
        while($row = mysqli_fetch_assoc($single_type)){
            $response = $row;
        }
    }
    echo json_encode($response);
}



//update the datails of the room type

if(isset($_POST['roomTypeId'])){
             
    $updateId = $_POST['roomTypeId'];
    $roomType = ucfirst($_POST['editRoomTypeName']);
    $roomCost =  $_POST['editRoomCost'];
    $desc =  $_POST['editDescription'];
    $status =  $_POST['editStatus'];
    $fileName = $_FILES['editRoomTypeImage']['name'];
    $tempname = $_FILES['editRoomTypeImage']['tmp_name'];
    $folder = "../assets/picture/RoomType/".$fileName;
    
    $sql_roomType = "select * from room_type where roomTypeId = '$updateId'";
    $result_room = mysqli_query($con,$sql_roomType);
    $num_rooms = mysqli_num_rows($result_room);
    
    
    $sendData = array();
    if ($num_rooms==0) {
        $error="Room Type is Not avaiable!";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );
        echo json_encode($sendData);
    } else {
        
        // query validation
        $sql_duplicate = "select * from room_type where roomType like '$roomType' AND RoomTypeId <> '$updateId' ";
        $result_dup = mysqli_query($con,$sql_duplicate);
        $nums = mysqli_num_rows($result_dup);

        // already available room type 
        if($nums>0){
            $error ="Room Type Name is Already Available";
            $sendData = array(
                "msg"=>"",
                "error"=>$error
            );
            echo json_encode($sendData);
        }
        else{


            $update="UPDATE room_type SET  RoomType='$roomType', RoomImage ='$fileName',Description='$desc',Status='$status',Cost='$roomCost' where RoomTypeId = '$updateId'" ;
        
        
            if(mysqli_query($con,$update))
            {
                            if(!move_uploaded_file($tempname, $folder)){
                                //if(false){
                                    $error ="Error in Updation ...! Try after sometime";
                                $sendData = array(
                                    "msg"=>"",
                                    "error"=>$error
                                );
                                echo json_encode($sendData);
                            }else{
                                $message = "Room Type details updated";
                              // message("user.php","User Added");
                              $sendData = array(
                                  "msg"=>$message,
                                "error"=>""
                            );
                            echo json_encode($sendData);
                        }
                        }
                        else{
                            $error ="Error in Updation ...! Try after sometime update";
                            $sendData = array(
                                "msg"=>"",
                                "error"=>$error
                            );
                            echo json_encode($sendData);
                            
                        }
    
                        
                        
                    }
        }

                
    }
    
      
// ---------------------------------------------Room Actions ---------------------------------------------
if(isset($_POST['roomNumber'])){
    $roomType = $_POST['roomType'];
    $roomNumber =  $_POST['roomNumber'];
    $validation = "select * from room_list where RoomNumber = '$roomNumber' ";
    $validation_result = mysqli_query($con,$validation);
    $validation_num = mysqli_num_rows($validation_result);
    $sendData = array();
    
if($validation_num>=1){
    $error ="The room number is already exists";
    $sendData = array(
        "msg"=>"",
        "error"=>$error
    );
}
else{
    $insert_query = "insert into room_list(RoomTypeId,RoomNumber) values('$roomType','$roomNumber')";
    
    if(mysqli_query($con,$insert_query)){
        
        $message = "Room is Added";
        $sendData = array(
            "msg"=>$message,
            "error"=>""
        );
   
        
    }
    
    else{

        $error ="Error in Adding ...! Try after sometime";
     $sendData = array(
         "msg"=>"",
         "error"=>$error
        );

    }
}
    

echo json_encode($sendData);
}

if(isset($_POST['deleteRoom'])){
    $roomId = $_POST['roomId'];
    $query_deleteRoom = "Delete from room_list where RoomId = '$roomId' ";
    $sendData = array();
    if(mysqli_query($con,$query_deleteRoom)){
        $sendData = array(
            "msg"=>"This Room  is Deleted",
            "error"=>""
        );
        echo json_encode($sendData);
    }else{
        $error = "This Room  Has a Booking";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );
        echo json_encode($sendData);
    }
}


//update - getting the selected room  details

if(isset($_POST['roomUpdateId'])){
    $roomUpdateId = $_POST['roomUpdateId'];
   
    $query_type =  "SELECT rt.RoomTypeId,rt.RoomType,rl.RoomNumber,rl.Status FROM room_list rl  inner join room_type rt on  rl.RoomTypeId = rt.RoomTypeId WHERE rl.RoomId like '$roomUpdateId' ";
    $single_type = mysqli_query($con,$query_type);
    $num_of_rows = mysqli_num_rows($single_type);
    $response = array();
    if($num_of_rows<1){
        $response['status'] = 200;
        $response['message'] = "invalid user id";
    }else{
        while($row = mysqli_fetch_assoc($single_type)){
            $response = $row;
        }
    }
    echo json_encode($response);
}




//update the datails of room table

if(isset($_POST['updateRoomId'])){
             
    $updateRoomId = $_POST['updateRoomId'];
    $editRoomType = $_POST['editRoomType'];
    $editRoomNumber = $_POST['editRoomNumber'];
    $editStatus =  $_POST['editStatus'];
   
  
    
    $validation = "select * from room_list where RoomNumber = '$editRoomNumber' AND RoomId <> '$updateRoomId'";
    $validation_result = mysqli_query($con,$validation);
    $validation_num = mysqli_num_rows($validation_result);
    
    $sendData = array();

    if($validation_num>=1){
    $error ="The room number is already exists";
    $sendData = array(
    "msg"=>"",
    "error"=>$error
    );

    } else {
        
        // query validation
        $update="UPDATE room_list SET  RoomTypeId='$editRoomType',RoomNumber='$editRoomNumber',Status='$editStatus' where roomId = '$updateRoomId'" ;
        
        
        if(mysqli_query($con,$update))
        {
                       
                            $message = "Room details updated";
                          // message("user.php","User Added");
                          $sendData = array(
                              "msg"=>$message,
                            "error"=>""
                        );
                     
                    
            }
            else{
                $error ="Error in Updation ...! Try after sometime update";
                $sendData = array(
                    "msg"=>"",
                    "error"=>$error
                );
                
            }

                    
                    
        }
        echo json_encode($sendData);

        
    }

    // -------------------------------------------- Booking Actions ------------------------------------- 
    //update - getting the selected room  details

if(isset($_POST['bookingDetail'])){
    $ID = $_POST['bookingId'];
   
    $selectBooking =   "SELECT rm.*,rt.RoomType,rl.RoomNumber,us.FirstName,us.ContactNo FROM room_booking rm 
                        inner join room_list rl on rl.RoomId = rm.RoomId
                        inner join room_type rt on rl.RoomTypeId = rt.RoomTypeId 
                        inner join users_details us on us.Userid = rm.User_id 
                        where rm.BookingId = $ID ";
                    

    $single_booking = mysqli_query($con,$selectBooking);
    $num_of_rows = mysqli_num_rows($single_booking);
    $response = array();
    if($num_of_rows<1){
        $response['status'] = 200;
        $response['message'] = "invalid booking id";
    }else{
        while($row = mysqli_fetch_assoc($single_booking)){
            $response = $row;
        }
    }
    echo json_encode($response);
}

// -------------------------------------- Event Type Actions ---------------------------------

//add new type of Event
if(isset($_POST['eventTypeName'])){

    $eventType =ucfirst($_POST['eventTypeName']);
    $eventCost =  $_POST['eventCost'];
    $desc =  $_POST['description'];
    $fileName = $_FILES['eventTypeImage']['name'];
    $tempname = $_FILES['eventTypeImage']['tmp_name'];
    $folder = "../assets/picture/EventType/".$fileName;

    $sql_eventType = "select * from event_type where eventType like '$eventType'";
    $result_room = mysqli_query($con,$sql_eventType);
    $num_events = mysqli_num_rows($result_room);

    $sendData = array();
    if($num_events>=1){
        $sendData = array(
            "msg" => "",
            "error"  => "Event Type is already Exist"
        );
    }
    else{
       $insert_query = "insert into event_type(EventType,EventImage,Cost,Description) values('$eventType','$fileName','$eventCost','$desc')";
       
       if(mysqli_query($con,$insert_query)){

        if(!move_uploaded_file($tempname, $folder)){
        
            $error ="Error in Adding ...! Try after sometime";
            $sendData = array(
                "msg"=>"",
                "error"=>$error
            );
           
        }
        else{
    
          $message = "Event Type is Added";
            $sendData = array(
                "msg"=>$message,
                "error"=>""
            );
       
        }
       }
       else{

        $error ="Error in Adding ...! Try after sometime";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );

       }
    }
    echo json_encode($sendData);
}


//update - getting the selected event type details

if(isset($_POST['eventTypeUpdateId'])){
    $eventTypeUpdateId = $_POST['eventTypeUpdateId'];
   
    $query_type = "select * from event_type where EventTypeId = '$eventTypeUpdateId' ";
    $single_type = mysqli_query($con,$query_type);
    $num_of_rows = mysqli_num_rows($single_type);
    $response = array();
    if($num_of_rows<1){
        $response['status'] = 200;
        $response['message'] = "invalid user id";
    }else{
        while($row = mysqli_fetch_assoc($single_type)){
            $response = $row;
        }
    }
    echo json_encode($response);
}

  //update the datals of event type table

  if(isset($_POST['editEventTypeName'])){
             
    $updateId = $_POST['eventTypeId'];
    $Type = ucfirst($_POST['editEventTypeName']);
    $Cost =  $_POST['editEventCost'];
    $desc =  $_POST['editDescription'];
    $status =  $_POST['editStatus'];
    $fileName = $_FILES['editEventTypeImage']['name'];
    $tempname = $_FILES['editEventTypeImage']['tmp_name'];
    $folder = "../assets/picture/EventType/".$fileName;
    
    $sql_Type = "select * from event_type where EventtypeId = '$updateId'";
    $result = mysqli_query($con,$sql_Type);
    $num_events = mysqli_num_rows($result);
    
    
    $sendData = array();
    if ($num_events==0) {
        $error="Event Type is Not avaiable!";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );
        echo json_encode($sendData);
    } else {
        

        $sql_eventType = "select * from event_type where eventType like '$Type' AND EventTypeId <> '$updateId'";
        $result_dup = mysqli_query($con,$sql_eventType);
        $nums = mysqli_num_rows($result_dup);

        if($nums>0){
            $error ="The Event Type is Already Available!";
            $sendData = array(
                "msg"=>"",
                "error"=>$error
            );
            echo json_encode($sendData);
        }
        else{


        // query validation
        $update="UPDATE event_type SET  EventType='$Type', EventImage ='$fileName', Description='$desc',Status='$status',Cost='$Cost' where EventTypeId = '$updateId'" ;
        
        
        if(mysqli_query($con,$update))
        {
                        if(!move_uploaded_file($tempname, $folder)){
                            //if(false){
                                $error ="Error in Updation ...! Try after sometime";
                            $sendData = array(
                                "msg"=>"",
                                "error"=>$error
                            );
                            echo json_encode($sendData);
                        }else{
                            $message = "Event Type details updated";
                          // message("user.php","User Added");
                          $sendData = array(
                              "msg"=>$message,
                              "error"=>""
                        );
                        echo json_encode($sendData);
                    }
                    }
                    else{
                        $error ="Error in Updation ...! Try after sometime update";
                        $sendData = array(
                            "msg"=>"",
                            "error"=>$error
                        );
                        echo json_encode($sendData);
                        
                    }

                    
                    
                }
                
        }


    }


    
if(isset($_POST['deleteEventType'])){
    $type_id = $_POST['typeId'];
    $query_deleteUser = "Delete from event_type where EventTypeId = '$type_id' ";
    $sendData = array();
    if(mysqli_query($con,$query_deleteUser)){
        $sendData = array(
            "msg"=>"This Event Type is Deleted",
            "error"=>""
        );
        echo json_encode($sendData);
    }else{
        $error = "This Event Type Has a Events in Hotel";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );
        echo json_encode($sendData);
    }
}
        



// ---------------------------------------------Event Actions ---------------------------------------------
if(isset($_POST['hallNumber'])){
    $eventType = $_POST['eventType'];
    $hallNumber =  $_POST['hallNumber'];
    $validation = "select * from event_list where hallNumber = '$hallNumber' ";
    $validation_result = mysqli_query($con,$validation);
    $validation_num = mysqli_num_rows($validation_result);
    $sendData = array();
    
if($validation_num>=1){
    $error ="The hall number is already exists";
    $sendData = array(
        "msg"=>"",
        "error"=>$error
    );
}
else{
    $insert_query = "insert into event_list(EventTypeId,HallNumber) values('$eventType','$hallNumber')";
    
    if(mysqli_query($con,$insert_query)){
        
        $message = "Event is Added";
        $sendData = array(
            "msg"=>$message,
            "error"=>""
        );
   
        
    }
    
    else{

        $error ="Error in Adding ...! Try after sometime";
     $sendData = array(
         "msg"=>"",
         "error"=>$error
        );

    }
}
    

echo json_encode($sendData);
}

// delete the event from the data base

if(isset($_POST['deleteEvent'])){
    $eventId = $_POST['eventId'];
    $query_deleteRoom = "Delete from event_list where EventId = '$eventId' ";
    $sendData = array();
    if(mysqli_query($con,$query_deleteRoom)){
        $sendData = array(
            "msg"=>"This Hall  is Deleted",
            "error"=>""
        );
        echo json_encode($sendData);
    }else{
        $error = "This Hall  Has a Booking";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );
        echo json_encode($sendData);
    }
}


//update - getting the selected event  details

if(isset($_POST['eventUpdateId'])){
    $eventUpdateId = $_POST['eventUpdateId'];
   
    $query_type =  "SELECT et.EventTypeId,et.EventType,el.HallNumber,el.Status FROM event_list el  
                    inner join event_type et on  el.EventTypeId = et.EventTypeId
                    WHERE el.EventId like '$eventUpdateId' ";

    $single_type = mysqli_query($con,$query_type);
    $num_of_rows = mysqli_num_rows($single_type);
    $response = array();
    if($num_of_rows<1){
        $response['status'] = 200;
        $response['message'] = "invalid event id";
    }else{
        while($row = mysqli_fetch_assoc($single_type)){
            $response = $row;
        }
    }
    echo json_encode($response);
}




//update the datails of event table

if(isset($_POST['updateEventId'])){
             
    $updateEventId = $_POST['updateEventId'];
    $editEventType = $_POST['editEventType'];
    $editHallNumber = $_POST['editHallNumber'];
    $editStatus =  $_POST['editStatus'];
   
  
    
    $validation = "select * from event_list where HallNumber = '$editHallNumber' AND EventID <> '$updateEventId' ";
    $validation_result = mysqli_query($con,$validation);
    $validation_num = mysqli_num_rows($validation_result);
    
    $sendData = array();

    if($validation_num>=1){
    $error ="The Hall number is already exists";
    $sendData = array(
    "msg"=>"",
    "error"=>$error
    );

    } else {
        
        // query validation
        $update="UPDATE event_list SET  EventTypeId='$editEventType',HallNumber='$editHallNumber',Status='$editStatus' where EventId = '$updateEventId'" ;
        
        
        if(mysqli_query($con,$update))
        {
                       
                            $message = "Hall details updated";
                          // message("user.php","User Added");
                          $sendData = array(
                              "msg"=>$message,
                            "error"=>""
                        );
                     
                    
            }
            else{
                $error ="Error in Updation ...! Try after sometime update";
                $sendData = array(
                    "msg"=>"",
                    "error"=>$error
                );
                
            }

                    
                    
        }
        echo json_encode($sendData);

        
    }

        // -------------------------------------------- Event Booking Actions ------------------------------------- 
    // getting the selected Event Booking details

if(isset($_POST['eventBookingDetail'])){
    $ID = $_POST['bookingId'];
   
    $selectBooking = "SELECT em.*,et.EventType,el.HallNumber,us.FirstName,us.ContactNo FROM event_booking em 
                      inner join event_list el on el.EventId = em.EventId
                      inner join event_type et on el.EventTypeId = et.EventTypeId 
                      inner join users_details us on us.Userid = em.User_id 
                      where em.BookingId = '$ID'";



    $single_booking = mysqli_query($con,$selectBooking);
    $num_of_rows = mysqli_num_rows($single_booking);
    $response = array();
    if($num_of_rows<1){
        $response['status'] = 200;
        $response['message'] = "invalid booking id";
    }else{
        while($row = mysqli_fetch_assoc($single_booking)){
            $response = $row;
        }
    }
    echo json_encode($response);
}

// ---------------------------------------------- Contact Actions ------------------------------------

// delete of contact
if(isset($_POST['deleteContact'])){
    $id = mysqli_real_escape_string($con, $_POST['ID']);
    $query_deleteUser = "Delete from contact where ID = '$id' ";
    $sendData = array();
    if(mysqli_query($con,$query_deleteUser)){
        $sendData = array(
            "msg"=>"Detail is Deleted",
            "error"=>""
        );
        echo json_encode($sendData);
    }else{
        $error = "Error in Deleting, Try After Sometimes";
        $sendData = array(
            "msg"=>"",
            "error"=>$error
        );
        echo json_encode($sendData);
    }
}
// ----------------------------------- Account ---------------------------------------
if(isset($_POST['userUpdateId'])){
    $userID = $_POST['userUpdateId'];
    $query = "SELECT * FROM users_details WHERE UserId = '$userID'";
    $result = mysqli_query($con, $query);
    $num = mysqli_num_rows($result);
    $response = [];

    if($num < 1){
        $response['status'] = 200;
        $response['message'] = "invalid user id";
    } else {
        $response = mysqli_fetch_assoc($result); // ✅ This should contain FirstName, etc.
    }

    echo json_encode($response);
}


//update the datals of user table

if (isset($_POST['updateAccount'])) {
    $user_id = mysqli_real_escape_string($con, trim($_POST['updateAccount']));
    $firstname = mysqli_real_escape_string($con, trim($_POST['firstname']));
    $lastname = mysqli_real_escape_string($con, trim($_POST['lastname']));
    $contactno = mysqli_real_escape_string($con, trim($_POST['contactno']));
    $gender = mysqli_real_escape_string($con, trim($_POST['gender']));

    // Optional profile image
    $profileImageName = $_FILES["profileImage"]["name"];
    $tempname = $_FILES["profileImage"]["tmp_name"];
    $folder = "../assets/picture/profiles/" . $profileImageName;

    $sendData = [];

    // --- Validations ---
    if ($firstname === "admin") {
        $sendData = ["msg" => "", "error" => "Invalid name. You can't use 'admin' as a first name."];
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $firstname) || !preg_match("/^[a-zA-Z-' ]*$/", $lastname)) {
        $sendData = ["msg" => "", "error" => "Names can only contain letters, spaces, hyphens and apostrophes."];
    } elseif (!preg_match("/^\+[0-9]{10,15}$/", $contactno)) {
        $sendData = ["msg" => "", "error" => "Phone number must be in international format, e.g., +2519XXXXXXXX."];
    } elseif (!in_array($gender, ['male', 'female'])) {
        $sendData = ["msg" => "", "error" => "Invalid gender selection."];
    } else {
        // Build update query dynamically based on whether an image is uploaded
        $update = "UPDATE users_details SET 
                    FirstName = '$firstname',
                    LastName = '$lastname',
                    ContactNo = '$contactno',
                    Gender = '$gender'";

        if (!empty($profileImageName)) {
            $update .= ", ProfileImage = '$profileImageName'";
        }

        $update .= " WHERE UserId = '$user_id'";

        if (mysqli_query($con, $update)) {
            // Move uploaded file only if an image was uploaded
            if (!empty($profileImageName)) {
                move_uploaded_file($tempname, $folder);
            }
            $sendData = ["msg" => "User details updated successfully!", "error" => ""];
        } else {
            $sendData = ["msg" => "", "error" => "Error during update. Please try again."];
        }
    }

    echo json_encode($sendData);
}


// -------------------------------- Change password -----------------------------------

if (isset($_POST["oldPassword"])) {
    $old = $_POST['oldPassword'];
    $new = $_POST['newPassword'];
    $ID = $_POST['change_password'];

    $sendData = [];

    $Q = "SELECT * FROM users_details WHERE UserId = '$ID'";
    $res = mysqli_query($con, $Q);
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        if (password_verify($old, $row['Password'])) {

            if (strlen($new) < 8 ||
                !preg_match('@[0-9]@', $new) ||
                !preg_match('@[A-Z]@', $new) ||
                !preg_match('@[a-z]@', $new) ||
                !preg_match('@[^\w]@', $new)) {
                $sendData = ["msg" => "", "error" => "New password must be at least 8 characters and contain uppercase, lowercase, number, and special character."];
            } else {
                $hashed = password_hash($new, PASSWORD_DEFAULT);
                $Q_update = "UPDATE users_details SET Password = '$hashed' WHERE UserId = '$ID'";
                mysqli_query($con, $Q_update);
                $sendData = ["msg" => "Password Changed Successfully!", "error" => ""];
            }

        } else {
            $sendData = ["msg" => "", "error" => "Oops! Wrong Old Password"];
        }
    } else {
        $sendData = ["msg" => "", "error" => "Invalid User ID"];
    }

    echo json_encode($sendData);
}


// -------------------------------- General Setting-----------------------------------------

if(isset($_POST["generalSettings"])){
   

    $Q = "SELECT * FROM general_settings ";
    $res = mysqli_query($con,$Q);
    $row = mysqli_fetch_assoc($res);

    $sendData =array();
    if($row){
        $sendData = $row;
    }else{
        $error ="Invalid User ID ";
        $sendData = array(
          "msg"=>"",
          "error"=>$error
      );
    }

    echo json_encode($sendData);
}

if(isset($_POST['companyName'])){
    $companyName = $_POST['companyName'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $City = $_POST['city'];
    $State = $_POST['state'];
    $Country = $_POST['country'];
    $zip = $_POST['zip'];
    $description = $_POST['description'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $teleNumber = $_POST['teleNumber'];
    $gs_id = $_POST['gs_id'];

   

    $Q = "SELECT * FROM general_settings Where ID = '$gs_id'";
    $res = mysqli_query($con,$Q);
    $row = mysqli_fetch_assoc($res);
    $num = mysqli_num_rows($res);
  
   
    $sendData = array();
    if($num>0){

        $Q_update = "UPDATE general_settings SET Name = '$companyName',Address_line1 = '$address1',Address_line2 = '$address2',
                     City = '$City', State = '$State', Country = '$Country', Zip_code = '$zip',Description = '$description',
                     Email = '$email',Phone_number = '$phoneNumber', Telephone_Number = '$teleNumber'
                     Where ID = '$gs_id'";
      
        if(mysqli_query($con,$Q_update)){
            $msg = "Company Details has been saved";
            $sendData = array(
                "msg"=>$msg,
                "error"=>""
            );
        }else{
            $error ="Oops! Error in Updation";
            $sendData = array(
              "msg"=>"",
              "error"=>$error
          );
        }
    }else{

        $error ="Invalid ID ";
        $sendData = array(
          "msg"=>"",
          "error"=>$error
      );
    }
 echo json_encode($sendData);

}
  
?>
