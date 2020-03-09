<?php

require_once('../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && isset($_GET['inspection']) && isset($_GET['investor']) && !empty($_GET['inspection'])
    && !empty($_GET['investor']) && !empty($_GET{'id'}) &&
    !empty(isset($_GET['token'])) && $_GET['auth'] == '527c3cc5633a9b5ff37f5dade8942166915b6989d38e94f943b377a77719ebcf') {
    require("../../../connection.php");
    $id = $_GET['id'];
    $token = $_GET['token'];
    $inspection = $_GET['inspection'];
    $investor = $_GET['investor'];
    $type = 1;
    $activation = 1;
    $inspected = 1;

    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        $getUsers = $conn->prepare("SELECT user_investor.organization, user_investor.name, user_investor.last_name, 
                    profile_investor.profile_picture, inspection.description FROM profile_investor JOIN user_investor JOIN user_entrepreneur JOIN inspection WHERE profile_investor.id_investor=user_investor.id 
                    AND inspection.inspected=? AND inspection.id_entrepreneur=user_entrepreneur.id AND inspection.id_investor=user_investor.id AND inspection.id_entrepreneur=? AND inspection.id_inspection=? AND inspection.id_investor=?");
        $getUsers->bind_param("iiii", $inspected, $id, $inspection, $investor);
        $getUsers->execute();
        $getUsersResults = $getUsers->get_result();

        if ($getUsersResults->num_rows > 0) {
            $row = $getUsersResults->fetch_assoc();

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

