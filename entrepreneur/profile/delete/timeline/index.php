<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == 'f3952cde77f55eff87419f14a2f1680d8553b75d0850f0d64f138613d650b131' && $_POST['type'] == 1) {
    if (isset($_POST['id_timeline']) && !empty($_POST['id_timeline'])) {
        require("../../../../connection.php");
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_timeline = $_POST['id_timeline'];

        if (Validation::VerifyUser($id, $type, $token, $conn) == true){
            $updateStatement = $conn->prepare("DELETE FROM `timeline_entrepreneur` WHERE id_entrepreneur=? AND id_timeline=?");
            $updateStatement->bind_param("ii",  $id, $id_timeline);
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
