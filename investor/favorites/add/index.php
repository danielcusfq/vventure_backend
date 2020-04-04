<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (!empty($_POST['entrepreneur']) && !empty($_POST['id']) && !empty($_POST['token'])
    && $_POST['auth'] == "62a9addde6deec5dc3e747a592649ddc40dc3077823d8d8fae2c10b3d97a36b3" ){
    require("../../../connection.php");
    //gets data
    $entrepreneur = $_POST['entrepreneur'];
    $id = $_POST['id'];
    $token = $_POST['token'];
    $type = 2;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //check if user is in favorites
        if(Validation::IsInFavorites($id, $entrepreneur, $type, $conn) == false){
            //prepares query
            $insertStmt = $conn->prepare("INSERT INTO `favorites_investor` (`id_investor`, `id_entrepreneur`) VALUES (?,?)");
            $insertStmt->bind_param("ii", $id, $entrepreneur);
            $insertStmt->execute();

            //sends response
            $myObj->res = "success";
            $JSON = json_encode($myObj);
            echo $JSON;
        } else {
            $myObj->res = "success";
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
