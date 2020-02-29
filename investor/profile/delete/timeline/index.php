<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == '93635062c7236dfd307a91bbb199055d2c200dae922e8a7f63d3e096781819ed' && $_POST['type'] == 2) {
    if (isset($_POST['id_timeline']) && !empty($_POST['id_timeline'])) {
        require("../../../../connection.php");
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_timeline = $_POST['id_timeline'];

        if (Validation::VerifyUser($id, $type, $token, $conn) == true){
            $updateStatement = $conn->prepare("DELETE FROM `timeline_investor` WHERE id_investor=? AND id_timeline=?");
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
