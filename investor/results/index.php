<?php
$myObj = (object)array();

if (isset($_GET["auth"]) && $_GET["auth"] = "9275b806411f4d3f3285ba9022c798d7ca48ab704a8d09a1dc6752522cbe1c73" && isset($_GET["last"])){

} else if (isset($_GET["auth"]) && $_GET["auth"] = "9275b806411f4d3f3285ba9022c798d7ca48ab704a8d09a1dc6752522cbe1c73") {
    require_once ("../../connection.php");

    $activation = 1;
    $getUsers =$conn->prepare("SELECT user_entrepreneur.id, user_entrepreneur.name, user_entrepreneur.last_name, user_entrepreneur.organization, 
                    profile_entrepreneur.profile_picture FROM profile_entrepreneur JOIN user_entrepreneur WHERE profile_entrepreneur.id_entrepreneur=user_entrepreneur.id 
                    AND user_entrepreneur.activation=?");
    $getUsers->bind_param("i", $activation);
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
            $userInfo->name = $row['name'];
            $userInfo->last = $row['last_name'];
            $userInfo->organization = $row['organization'];
            $userInfo->image = $row['profile_picture'];

            $lastUser = $row['id'];
            array_push($users, $userInfo);
        }
    } else {
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
