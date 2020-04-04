<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST['auth']) && isset($_POST['entrepreneur']) && isset($_POST['id']) && isset($_POST['token']) && isset($_POST['inspection']) &&
    !empty($_POST['entrepreneur']) && !empty($_POST['id']) && !empty($_POST['token']) && !empty($_POST['inspection']) && $_POST['auth']
    == "8916c77882b8dd2ca9ceccf58c275a26b975d3bb47269376312e41569c47430e" ){
    require("../../../connection.php");
    //gets data
    $entrepreneur = $_POST['entrepreneur'];
    $id = $_POST['id'];
    $token = $_POST['token'];
    $inspection = $_POST['inspection'];
    $type = 2;
    $description = "";
    $inspected = 1;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //prepares query
        $updateStatement = $conn->prepare("UPDATE `inspection` SET `description`=?, `inspected`=? WHERE id_investor=? AND id_entrepreneur=? AND id_inspection=?");
        $updateStatement->bind_param("siiii", $description, $inspected, $id, $entrepreneur, $inspection);
        $updateStatement->execute();

        //send response
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

