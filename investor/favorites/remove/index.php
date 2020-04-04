<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (!empty($_POST['entrepreneur']) && !empty($_POST['id']) && !empty($_POST['token'])
    && $_POST['auth'] == "7ec53e6efe4bee1bcba65122e6e67f38c8224658b3f86c8681df5fe77d33b3c2") {
    require("../../../connection.php");
    //gets data
    $entrepreneur = $_POST['entrepreneur'];
    $id = $_POST['id'];
    $token = $_POST['token'];
    $type = 2;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //checks user is in favorites
        if (Validation::IsInFavorites($id, $entrepreneur, $type, $conn) == true) {
            //prepares query
            $insertStmt = $conn->prepare("DELETE FROM `favorites_investor` WHERE `id_investor`=? AND `id_entrepreneur`=?");
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

