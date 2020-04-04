<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == "85e11a30110d8687f39caa7eef251a929bee3415ee4923d3f93697b986dab799") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty
        ($_POST['token']) && $_POST['type'] == 2) {
        if (isset($_POST['background']) && !empty($_POST['background'])) {
            require("../../../../connection.php");
            //gets data
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $background = $_POST['background'];

            //validates user
            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                //prepare query
                $updateStatement = $conn->prepare("UPDATE `profile_investor` SET `background`=? WHERE id_investor=?");
                $updateStatement->bind_param("si", $background, $id);
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

