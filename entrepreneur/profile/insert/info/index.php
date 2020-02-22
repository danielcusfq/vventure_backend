<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "6523cde886413d7237021657b6fee69873e30376c4dbbb72ebf114f506d423d9") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['detail']) && !empty($_POST['detail']) && isset($_POST['title']) && !empty($_POST['title'])) {
            require ("../../../../connection.php");
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $detail = $_POST['detail'];
            $title = $_POST['title'];
            $pos = 99;

            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $insertStmt = $conn->prepare("INSERT INTO `info_entrepreneur` (`id_entrepreneur`, `title`, `detail`, `position`) VALUES (?,?,?,?)");
                $insertStmt->bind_param("issi", $id, $title, $detail, $pos);
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
