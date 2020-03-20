<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (!empty($_GET['id']) && !empty($_GET['token']) && $_GET['auth'] == "98266603212edabf6e53e3b485924814f3df41eb38aec6edbe2f2feb5e5767d3" ){
    require("../../../connection.php");
    $id = $_GET['id'];
    $token = $_GET['token'];
    $type = 1;

    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        $getUsers = $conn->prepare("SELECT user_investor.id, user_investor.name, user_investor.last_name, user_investor.organization, 
                    profile_investor.profile_picture FROM profile_investor JOIN user_investor JOIN favorites_entrepreneur WHERE profile_investor.id_investor=user_investor.id AND 
                    favorites_entrepreneur.id_investor=user_investor.id AND favorites_entrepreneur.id_entrepreneur=? ORDER BY user_investor.id DESC ");
        $getUsers->bind_param("i",$id);
        $getUsers->execute();
        $getUsersResults = $getUsers->get_result();

        $users = array();
        $lastUser = null;

        if ($getUsersResults->num_rows > 0) {
            while($row = $getUsersResults->fetch_assoc()){
                $userInfo = (Object) array();

                $userInfo->id = $row['id'];
                $userInfo->name = $row['name'];
                $userInfo->last = $row['last_name'];
                $userInfo->organization = $row['organization'];
                $userInfo->image = $row['profile_picture'];

                $lastUser = $row['id'];
                array_push($users, $userInfo);
            }
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
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}
