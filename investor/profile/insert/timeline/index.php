<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "9d529201df14dbcbca856cbee54afc25e0f2ac6ef2987f01da555ec25e81b3a3") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty
        ($_POST['token']) && $_POST['type'] == 2) {
        if (isset($_POST['detail']) && !empty($_POST['detail'])) {
            require ("../../../../connection.php");
            //gets data
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $detail = $_POST['detail'];
            $pos = 99;

            //validates user
            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                //prepare query
                $insertStmt = $conn->prepare("INSERT INTO `timeline_investor` (`id_investor`, `description`, `position`) VALUES (?,?,?)");
                $insertStmt->bind_param("isi", $id, $detail, $pos);
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
