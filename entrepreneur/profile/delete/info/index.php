<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == '3542f67fa25e703491846af21cbf09007879f6f056427e36737ea33937ec6395' && $_POST['type'] == 1) {
    if (isset($_POST['id_info']) && !empty($_POST['id_info'])) {
        require("../../../../connection.php");
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_info = $_POST['id_info'];

        if (Validation::VerifyUser($id, $type, $token, $conn) == true){
            $updateStatement = $conn->prepare("DELETE FROM `info_entrepreneur` WHERE id_entrepreneur=? AND id_info=?");
            $updateStatement->bind_param("ii",  $id, $id_info);
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