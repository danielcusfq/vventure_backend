<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == "13496a7b21b744a01b08da955937251cff3e1cdac7189b485138a87d471aa3db") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['organization']) && !empty($_POST['organization'])) {
            require("../../../../connection.php");
            //gets data
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $organization = $_POST['organization'];

            //validates user
            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $updateStatement = $conn->prepare("UPDATE `user_entrepreneur` SET `organization`=? WHERE id=?");
                $updateStatement->bind_param("si", $organization, $id);
                $updateStatement->execute();

                //sends response
                $myObj->res = "success";
                $JSON = json_encode($myObj);
                echo $JSON;
            } else {
                $myObj->res = "user error";
                $JSON = json_encode($myObj);
                echo $JSON;
            }
        } else {
            $myObj->res = "no post info";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "error no auth";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}

