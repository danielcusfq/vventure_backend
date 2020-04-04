<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && isset($_GET['inspection']) && !empty($_GET['inspection']) && !empty($_GET{'id'}) &&
    !empty(isset($_GET['token'])) && $_GET['auth'] == 'cb7ee1818d60a7f8888a9c3d2125e9cf8a04ac5a417f6487a355caed5cac360a') {
    require("../../../connection.php");
    //gets data
    $id = $_GET['id'];
    $token = $_GET['token'];
    $inspection = $_GET['inspection'];
    $type = 2;
    $activation = 1;
    $inspected = 1;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //prepares query
        $getUsers = $conn->prepare("SELECT user_investor.organization, user_investor.name, user_investor.last_name, 
                    profile_investor.profile_picture, inspection.description FROM profile_investor JOIN user_investor JOIN user_entrepreneur JOIN inspection WHERE profile_investor.id_investor=user_investor.id 
                    AND inspection.inspected=? AND inspection.id_entrepreneur=user_entrepreneur.id AND inspection.id_investor=user_investor.id  AND inspection.id_inspection=? AND inspection.id_investor=?");
        $getUsers->bind_param("iii", $inspected,  $inspection, $id);
        $getUsers->execute();
        $getUsersResults = $getUsers->get_result();

        //fetch information
        if ($getUsersResults->num_rows > 0) {
            $row = $getUsersResults->fetch_assoc();

            //send response
            $myObj->res = "success";
            $myObj->name = $row['name'];
            $myObj->last = $row['last_name'];
            $myObj->organization = $row['organization'];
            $myObj->image = $row['profile_picture'];
            $myObj->detail = $row['description'];
            $JSON = json_encode($myObj);
            echo $JSON;
        } else {
            $myObj->res = "error no auth";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "user error";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}

