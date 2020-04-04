<?php

require_once('../../validation/Validation.php');
$myObj = (object)array();

if (isset($_GET["auth"]) && $_GET["auth"] = "0f049c3943613f61c699d434cbbd56817965cf3125ae8c012b4748fdb3044617" && !empty
        ($_GET['id']) && !empty($_GET['token']) && !empty($_GET['query'])) {
    require_once ("../../connection.php");
    //gets data
    $id = $_GET['id'];
    $token = $_GET['token'];
    $query = mysqli_real_escape_string($conn, $_GET['query']);
    $activation = 1;
    $type = 2;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //prepares query
        $getUsers =$conn->prepare("SELECT user_entrepreneur.id, profile_entrepreneur.stage, user_entrepreneur.organization, 
                    profile_entrepreneur.profile_picture FROM profile_entrepreneur JOIN user_entrepreneur WHERE profile_entrepreneur.id_entrepreneur=user_entrepreneur.id 
                    AND user_entrepreneur.activation=? AND (user_entrepreneur.name LIKE CONCAT( '%',?,'%') OR user_entrepreneur.last_name LIKE CONCAT( '%',?,'%') 
                    OR user_entrepreneur.organization LIKE CONCAT( '%',?,'%')  OR profile_entrepreneur.stage LIKE CONCAT( '%',?,'%')
                    OR profile_entrepreneur.stake_info LIKE CONCAT( '%',?,'%') OR profile_entrepreneur.problem LIKE CONCAT( '%',?,'%') OR profile_entrepreneur.solution LIKE CONCAT( '%',?,'%'))");
        $getUsers->bind_param("isssssss", $activation, $query, $query, $query, $query, $query, $query, $query);
        $getUsers->execute();
        $getUsersResults = $getUsers->get_result();

        $users = array();
        $lastUser = null;

        //fetch information
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

            //sends response
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
