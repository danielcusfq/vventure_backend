<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == "2a9f190211c1eebc8280b7e9b77f3f7b2f806d8f64f06fba81730ba455ecb7f6") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['detail']) && isset($_POST['id_info']) && isset($_POST['title']) && !empty($_POST['detail']) && !empty($_POST['id_info']) && !empty($_POST['title'])) {
            require("../../../../connection.php");
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $title = $_POST['title'];
            $description = $_POST['detail'];
            $id_info = $_POST['id_info'];

            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $updateStatement = $conn->prepare("UPDATE `info_entrepreneur` SET `title`=?, `detail`=? WHERE id_entrepreneur=? AND id_info=?");
                $updateStatement->bind_param("ssii", $title, $description, $id, $id_info);
                $updateStatement->execute();

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

