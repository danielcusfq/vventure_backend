<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST['auth']) && isset($_POST['entrepreneur']) && isset($_POST['id']) && isset($_POST['token']) && isset($_POST['inspection']) && isset($_POST['description']) &&
    !empty($_POST['entrepreneur']) && !empty($_POST['id']) && !empty($_POST['token']) && !empty($_POST['inspection']) && !empty
    ($_POST['description']) && $_POST['auth'] == "e2c1466c3f0d308de5c0aabf430b3f6ee24c5c9c28d3a51018dc5bdbd06af70a") {
    require("../../../connection.php");
    //gets data
    $entrepreneur = $_POST['entrepreneur'];
    $id = $_POST['id'];
    $token = $_POST['token'];
    $inspection = $_POST['inspection'];
    $type = 2;
    $description = $_POST['description'];
    $inspected = 1;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //prepares query
        $updateStatement = $conn->prepare("UPDATE `inspection` SET `description`=?, `inspected`=? WHERE id_investor=? AND id_entrepreneur=? AND id_inspection=?");
        $updateStatement->bind_param("siiii", $description, $inspected, $id, $entrepreneur, $inspection);
        $updateStatement->execute();

        //sends response
        $myObj->res = "success";
        $JSON = json_encode($myObj);
        echo $JSON;
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


