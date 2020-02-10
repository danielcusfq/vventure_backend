<?php
include ("../auth/Auth_Info.php");

$myObj = (object)array();

// verifies it comes from authorized device
if (isset($_POST["auth"]) && $_POST["auth"] == "607be6747e2a18f043221b6528785169e4a391fa17c12b45dc44289387bd9cbb"){
    require_once ("../connection.php");

    $type = mysqli_real_escape_string($conn, $_POST["type"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $email = $string = preg_replace('/\s+/','',$email);
    $email = strtolower($email);
    $password = $_POST["password"];

    // verifies the account is an entrepreneur
    if ($type == 1){
        $password = $password.$email."entrepreneur";
        $user = verify_user($conn, 1, $email, $password);

        if ($user->get_token() != null && $user->get_activation() != null){
            $token = $user->get_token();
            $activation = $user->get_activation();

            $myObj->res = "success";
            $myObj->type = "1";
            $myObj->token = $token;
            $myObj->activation = $activation;
            $JSON = json_encode($myObj);
            echo $JSON;
        } else {
            $myObj->res = "error no user info";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else if ($type == 2){ // verifies account is an investor
        $password = $password.$email."investor";
        $user = verify_user($conn, 2, $email, $password);

        if ($user->get_token() != null && $user->get_activation() != null){
            $token = $user->get_token();
            $activation = $user->get_activation();

            $myObj->res = "success";
            $myObj->type = "2";
            $myObj->token = $token;
            $myObj->activation = $activation;
            $JSON = json_encode($myObj);
            echo $JSON;
        } else {
            $myObj->res = "error no user info";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "error no type";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error malformed form";
    $JSON = json_encode($myObj);
    echo $JSON;
}

// authenticates user and returns authentication information
function verify_user($conn, $type, $email, $password){
    $validQuery = true;

    if ($type == 1){
        $validationStmt = $conn->prepare("SELECT `password`, `activation`, `token` FROM `user_entrepreneur` WHERE `email`=? ");
    } elseif ($type == 2){
        $validationStmt = $conn->prepare("SELECT `password`, `activation`, `token` FROM `user_investor` WHERE `email`=? ");
    } else {
        return false;
    }

    if ($validQuery == true){
        $validationStmt->bind_param("s", $email);
        $validationStmt->execute();
        $validationResult = $validationStmt->get_result();

        if($validationResult->num_rows > 0  && $validationResult->num_rows < 2) {
            $rowVal = $validationResult->fetch_assoc();
            if(password_verify($password, $rowVal["password"])) {
                $auth_info = new Auth_Info($rowVal["token"], $rowVal["activation"]);
                return $auth_info;
            } else {
                return new Auth_Info(null, null);
            }
        }
        else {
            return new Auth_Info(null, null);
        }
    } else {
        return new Auth_Info(null, null);
    }
}