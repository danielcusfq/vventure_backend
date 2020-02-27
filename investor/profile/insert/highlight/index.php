<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "13f4667e8740a0fccc7680b42b43bf89525aebf33b59343e279d36f61e5e96ea") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty
        ($_POST['token']) && $_POST['type'] == 2) {
        if (isset($_POST['detail']) && !empty($_POST['detail'])) {
            require ("../../../../connection.php");
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $detail = $_POST['detail'];

            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $insertStmt = $conn->prepare("INSERT INTO `highlights_investor` (`id_investor`, `description`) VALUES (?,?)");
                $insertStmt->bind_param("is", $id, $detail);
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
