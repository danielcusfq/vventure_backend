<?php
require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['investor']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])
    ) && !empty($_POST['investor']) && $_POST['auth'] == "54983ad0bc722a62d3072c5173ae7824b079eaa93cebb3c6425664f2210073d3"){
    require_once("../../../connection.php");
    $id = $_POST{'id'};
    $token = $_POST{'token'};
    $investor = $_POST['investor'];
    $type = 1;
    $description = "";
    $inspected = 0;

    if (Validation::VerifyUser($id, $type, $token, $conn) == true){
        $insertInspection = $conn->prepare("INSERT INTO `inspection` (`id_investor`, `id_entrepreneur`, `description`, `inspected`) VALUES (?,?,?,?)");
        $insertInspection->bind_param("iisi", $investor, $id, $description, $inspected);
        $insertInspection->execute();

        $myObj->res = "success";
        $JSON = json_encode($myObj);
        echo $JSON;
    } else {
        $myObj->res = "no valid user";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}
