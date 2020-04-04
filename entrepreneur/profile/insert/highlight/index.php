<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "f525ddc20d143230a7e3a2b4d6871ebf0bcbcc71816de9ae358ed6025a6f665f") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['detail']) && !empty($_POST['detail'])) {
            require ("../../../../connection.php");
            //gets all data
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $detail = $_POST['detail'];

            //validates user
            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                //prepare query
                $insertStmt = $conn->prepare("INSERT INTO `highlights_entrepreneur` (`id_entrepreneur`, `description`) VALUES (?,?)");
                $insertStmt->bind_param("is", $id, $detail);
                $insertStmt->execute();

                //sends response
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
