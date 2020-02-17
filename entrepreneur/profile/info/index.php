<?php

$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && !empty($_GET{'id'}) && !empty(isset($_GET['token'])) && $_GET['auth'] == "ee046aa3e8cba86d08f5c902c2dba507c7ec6c63da3cbc0262ff2e5d3f969854"){
    require_once ("../../../connection.php");
    $id = $_GET{'id'};
    $token = $_GET{'token'};

    $getProfile = $conn->prepare("SELECT user_entrepreneur.id, user_entrepreneur.organization, user_entrepreneur.name, user_entrepreneur.last_name, 
                    profile_entrepreneur.profile_picture, profile_entrepreneur.profile_video, profile_entrepreneur.stage, 
                    profile_entrepreneur.stake, profile_entrepreneur.stake_info, profile_entrepreneur.solution, profile_entrepreneur.problem FROM user_entrepreneur JOIN profile_entrepreneur WHERE 
                    user_entrepreneur.id=? AND user_entrepreneur.token=? AND user_entrepreneur.id=profile_entrepreneur.id_entrepreneur");
    $getProfile->bind_param("is", $id, $token);
    $getProfile->execute();
    $getProfileResults = $getProfile->get_result();

    if ($getProfileResults->num_rows == 1) {
        $row = $getProfileResults->fetch_assoc();

        $myObj->res = "success";
        $myObj->id = $row['id'];
        $myObj->organization = $row['organization'];
        $myObj->name = $row['name'];
        $myObj->last = $row['last_name'];
        $myObj->image = $row['profile_picture'];
        $myObj->video = $row['profile_video'];
        $myObj->stage = $row['stage'];
        $myObj->stake = $row['stake'];
        $myObj->stakeinfo = $row['stake_info'];
        $myObj->problem = $row['problem'];
        $myObj->solution = $row['solution'];
        $JSON = json_encode($myObj);
        echo $JSON;
    } else {
        $myObj->res = "error no data";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}
