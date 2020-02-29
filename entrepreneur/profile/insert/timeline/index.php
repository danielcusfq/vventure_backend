<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "6a391198f489808f558534e9076fa893a204bd622e8c6ce9f25133b934e4bb6f") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['detail']) && !empty($_POST['detail'])) {
            require ("../../../../connection.php");
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $detail = $_POST['detail'];
            $pos = 99;

            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $insertStmt = $conn->prepare("INSERT INTO `timeline_entrepreneur` (`id_entrepreneur`, `description`, `position`) VALUES (?,?,?)");
                $insertStmt->bind_param("isi", $id, $detail, $pos);
                $insertStmt->execute();

                $myObj->res = "success";
                $JSON = json_encode($myObj);
                echo $JSON;
            } else {
                $myObj->res = "invalid user";
                $JSON = json_encode($myObj);
                echo $JSON;
            }
        } else {
            $myObj->res = "error with detail data";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else{
        $myObj->res = "error no user info";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no auth info";
    $JSON = json_encode($myObj);
    echo $JSON;
}
