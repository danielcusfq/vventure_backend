<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == 'd3a068303bab39c65720afed00c62e8a58fbb72f9c499cda7322e1462475825b' && $_POST['type'] == 2) {
    if (isset($_POST['id_info']) && !empty($_POST['id_info'])) {
        require("../../../../connection.php");
        //gets data
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_info = $_POST['id_info'];

        //validates user
        if (Validation::VerifyUser($id, $type, $token, $conn) == true){
            //prepares query
            $updateStatement = $conn->prepare("DELETE FROM `info_investor` WHERE id_investor=? AND id_info=?");
            $updateStatement->bind_param("ii",  $id, $id_info);
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
