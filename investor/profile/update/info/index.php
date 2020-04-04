<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == "8212fd0968904f4af1655909e85fe0feb7e314c55ad38d111e5ee7366bc95d96") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty
        ($_POST['token']) && $_POST['type'] == 2) {
        if (isset($_POST['detail']) && isset($_POST['id_info']) && isset($_POST['title']) && !empty($_POST['detail']) && !empty($_POST['id_info']) && !empty($_POST['title'])) {
            require("../../../../connection.php");
            //gets data
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $title = $_POST['title'];
            $description = $_POST['detail'];
            $id_info = $_POST['id_info'];

            //validates user
            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                //prepares query
                $updateStatement = $conn->prepare("UPDATE `info_investor` SET `title`=?, `detail`=? WHERE id_investor=? AND id_info=?");
                $updateStatement->bind_param("ssii", $title, $description, $id, $id_info);
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

