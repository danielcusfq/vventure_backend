<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && !empty($_GET{'id'}) && !empty(isset($_GET['token'])) && $_GET['auth'] == '260371ba113efbd41d041970b40b22ce1b9d56b05710cf1453fa739bdd23e71e') {
    require("../../../connection.php");
    //gets data
    $id = $_GET['id'];
    $type = 1;
    $token = $_GET['token'];
    $activation = 1;
    $inspected = 1;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        $getUsers = $conn->prepare("SELECT inspection.id_inspection, user_investor.id,  user_investor.organization, user_investor.name, user_investor.last_name, 
                    profile_investor.profile_picture FROM profile_investor JOIN user_investor JOIN inspection WHERE profile_investor.id_investor=user_investor.id 
                    AND user_investor.activation=? AND inspection.inspected=? AND inspection.id_investor=user_investor.id AND inspection.id_entrepreneur=? ORDER BY inspection.id_inspection DESC ");
        $getUsers->bind_param("iii", $activation, $inspected, $id);
        $getUsers->execute();
        $getUsersResults = $getUsers->get_result();

        $users = array();
        $lastUser = null;
        $noUsers = true;

        //gets all data
        if ($getUsersResults->num_rows > 0) {
            $noUsers = false;
            while ($row = $getUsersResults->fetch_assoc()) {
                $userInfo = (Object)array();

                $userInfo->id = $row['id'];
                $userInfo->name = $row['name'];
                $userInfo->last = $row['last_name'];
                $userInfo->organization = $row['organization'];
                $userInfo->image = $row['profile_picture'];
                $userInfo->inspection = $row['id_inspection'];
                $lastUser = $row['id'];
                array_push($users, $userInfo);
            }

            //sends response
            $myObj->res = "success";
            $myObj->users = $users;
            $myObj->last = $lastUser;
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

