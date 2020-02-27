<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "ac4e7d80123eb6f32ce658fd967703539809265657db8b766855e13e31feb4fc") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty
        ($_POST['token']) && $_POST['type'] == 2) {
        if (isset($_POST['detail']) && !empty($_POST['detail']) && isset($_POST['title']) && !empty($_POST['title'])) {
            require ("../../../../connection.php");
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $detail = $_POST['detail'];
            $title = $_POST['title'];
            $pos = 99;

            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $insertStmt = $conn->prepare("INSERT INTO `info_investor` (`id_investor`, `title`, `detail`, `position`) VALUES (?,?,?,?)");
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
