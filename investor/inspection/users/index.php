<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token'])  && !empty($_GET{'id'}) && !empty(isset($_GET['token'])) && $_GET['auth'] == '355155b15b8f4acab45ac8cb623522b5c60d82a300429e3723c84853f6633ded') {
    require("../../../connection.php");
    $id = $_GET['id'];
    $type = 2;
    $token = $_GET['token'];
    $activation = 1;
    $inspected = 0;

    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        $getUsers =$conn->prepare("SELECT inspection.id_inspection, user_entrepreneur.id, profile_entrepreneur.stage, user_entrepreneur.organization, 
                    profile_entrepreneur.profile_picture FROM profile_entrepreneur JOIN user_entrepreneur JOIN inspection WHERE profile_entrepreneur.id_entrepreneur=user_entrepreneur.id 
                    AND user_entrepreneur.activation=? AND inspection.inspected=? AND inspection.id_entrepreneur=user_entrepreneur.id AND inspection.id_investor=?");
        $getUsers->bind_param("iii", $activation, $inspected, $id);
        $getUsers->execute();
        $getUsersResults = $getUsers->get_result();

        $users = array();
        $lastUser = null;
        $noUsers = true;

        if ($getUsersResults->num_rows > 0) {
            $noUsers = false;
            while($row = $getUsersResults->fetch_assoc()){
                $userInfo = (Object) array();

                $userInfo->id = $row['id'];
                $userInfo->stage = $row['stage'];
                $userInfo->organization = $row['organization'];
                $userInfo->image = $row['profile_picture'];
                $userInfo->inspection = $row['id_inspection'];
                $lastUser = $row['id'];
                array_push($users, $userInfo);
            }

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
