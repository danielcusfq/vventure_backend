<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == "927301fc7588331163dbe11c8168b2af9a92e46e5b3f339a8e06ce7c3daaa428") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['last']) && !empty($_POST['last'])) {
            require("../../../../connection.php");
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $name = $_POST['name'];
            $last = $_POST['last'];

            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $updateStatement = $conn->prepare("UPDATE `user_entrepreneur` SET `name`=?, `last_name`=? WHERE id=?");
                $updateStatement->bind_param("ssi", $name, $last, $id);
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
