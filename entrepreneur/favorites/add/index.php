<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (!empty($_POST['investor']) && !empty($_POST['id']) && !empty($_POST['token'])
    && $_POST['auth'] == "45717eb6e78890ab6746766b5ab3ec786102f2fdd945d6aa81eaa3d666d78026" ){
    require("../../../connection.php");
    //gets all data
    $investor = $_POST['investor'];
    $id = $_POST['id'];
    $token = $_POST['token'];
    $type = 1;

    //validates if user is valid
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //checks if user is in favorites
        if(Validation::IsInFavorites($id, $investor, $type, $conn) == false){
            $insertStmt = $conn->prepare("INSERT INTO `favorites_entrepreneur` (`id_entrepreneur`, `id_investor`) VALUES (?,?)");
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
