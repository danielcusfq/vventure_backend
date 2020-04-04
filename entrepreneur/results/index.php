<?php
$myObj = (object)array();

if (isset($_GET["auth"]) && $_GET["auth"] = "80d3d6348cf687df8c7fbd7dc901822f594a27e22a97e7ae10db253d3d3da684" && isset($_GET["last"])){

} else if (isset($_GET["auth"]) && $_GET["auth"] = "80d3d6348cf687df8c7fbd7dc901822f594a27e22a97e7ae10db253d3d3da684") {
    require_once ("../../connection.php");

    $activation = 1;
    //prepares select query
    $getUsers =$conn->prepare("SELECT user_investor.id, user_investor.name, user_investor.last_name, user_investor.organization, 
       profile_investor.profile_picture FROM profile_investor JOIN user_investor WHERE profile_investor.id_investor=user_investor.id AND user_investor.activation=?");
    $getUsers->bind_param("i", $activation);
    $getUsers->execute();
    $getUsersResults = $getUsers->get_result();

    $users = array();
    $lastUser = null;
    $noUsers = true;

    //fetch all users
    if ($getUsersResults->num_rows > 0) {
        $noUsers = false;
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
    } else {
        //sends response
        $myObj->res = "error no auth";
        $JSON = json_encode($myObj);
        echo $JSON;
    }

    if ($noUsers == false) {
        $myObj->res = "success";
        $myObj->users = $users;
        $myObj->last = $lastUser;
        $JSON = json_encode($myObj);
        echo $JSON;
    }

} else {
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}
