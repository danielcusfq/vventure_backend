<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (!empty($_GET['id']) && !empty($_GET['token']) && $_GET['auth'] == "ae620859b6016bcdde49fd9a8bcb932d720a863f51034fc9da6a4f21db39b2a5" ){
    require("../../../connection.php");
    //gets data
    $id = $_GET['id'];
    $token = $_GET['token'];
    $type = 2;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //prepares query
        $getUsers =$conn->prepare("SELECT user_entrepreneur.id, profile_entrepreneur.stage, user_entrepreneur.organization, 
                    profile_entrepreneur.profile_picture FROM profile_entrepreneur JOIN user_entrepreneur JOIN favorites_investor WHERE profile_entrepreneur.id_entrepreneur=user_entrepreneur.id 
                    AND  user_entrepreneur.id=favorites_investor.id_entrepreneur AND favorites_investor.id_investor=? ORDER BY user_entrepreneur.id DESC");
        $getUsers->bind_param("i",$id);
        $getUsers->execute();
        $getUsersResults = $getUsers->get_result();

        $users = array();
        $lastUser = null;

        //fetch user data
        if ($getUsersResults->num_rows > 0) {
            while($row = $getUsersResults->fetch_assoc()){
                $userInfo = (Object) array();

                $userInfo->id = $row['id'];
                $userInfo->stage = $row['stage'];
                $userInfo->organization = $row['organization'];
                $userInfo->image = $row['profile_picture'];

                $lastUser = $row['id'];
                array_push($users, $userInfo);
            }
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
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}
