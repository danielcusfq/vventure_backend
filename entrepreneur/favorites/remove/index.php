<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (!empty($_POST['investor']) && !empty($_POST['id']) && !empty($_POST['token'])
    && $_POST['auth'] == "0177863d5c69955fb6c0d628198f79469a183cb9dc1ab41ae33d8d1e5d54e8a4" ){
    require("../../../connection.php");
    $investor = $_POST['investor'];
    $id = $_POST['id'];
    $token = $_POST['token'];
    $type = 1;

    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        if(Validation::IsInFavorites($id, $investor, $type, $conn) == true){
            $insertStmt = $conn->prepare("DELETE FROM `favorites_entrepreneur` WHERE `id_entrepreneur`=? AND `id_investor`=?");
            $insertStmt->bind_param("ii", $id, $investor);
            $insertStmt->execute();

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
