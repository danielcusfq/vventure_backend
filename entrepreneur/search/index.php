<?php

require_once('../../validation/Validation.php');
$myObj = (object)array();

if (isset($_GET["auth"]) && $_GET["auth"] = "052dcfd3508f2f4dc59b02a77358a0e17fc07b10908cd10d681d24610437408f" && !empty
        ($_GET['id']) && !empty($_GET['token']) && !empty($_GET['query'])) {
    require_once ("../../connection.php");
    $id = $_GET['id'];
    $token = $_GET['token'];
    $query = mysqli_real_escape_string($conn, $_GET['query']);
    $activation = 1;
    $type = 1;

    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        $getUsers = $conn->prepare("SELECT user_investor.id, user_investor.name, user_investor.last_name, user_investor.organization, 
                    profile_investor.profile_picture FROM profile_investor JOIN user_investor WHERE profile_investor.id_investor=user_investor.id AND 
                    user_investor.activation=? AND (user_investor.name LIKE CONCAT( '%',?,'%') OR user_investor.last_name LIKE CONCAT( '%',?,'%') 
                    OR user_investor.organization LIKE CONCAT( '%',?,'%') OR  profile_investor.background LIKE CONCAT( '%',?,'%') OR profile_investor.interests LIKE CONCAT( '%',?,'%'))");
        $getUsers->bind_param("isssss", $activation, $query, $query, $query, $query, $query);
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

            $myObj->res = "success";
            $myObj->users = $users;
            $myObj->last = $lastUser;
            $JSON = json_encode($myObj);
            echo $JSON;
        } else {
            $myObj->res = "no match";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "auth error";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "auth error";
    $JSON = json_encode($myObj);
    echo $JSON;
}
