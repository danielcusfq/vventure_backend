<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == "424e36889a5fdde2549a5153410ccea88ea9aeee4955e26f57b7629966650a3c") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['problem']) && !empty($_POST['problem'])) {
            require("../../../../connection.php");
            //gets data
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $problem = $_POST['problem'];

            //validate user
            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $updateStatement = $conn->prepare("UPDATE `profile_entrepreneur` SET `problem`=? WHERE id_entrepreneur=?");
                $updateStatement->bind_param("si", $problem, $id);
                $updateStatement->execute();

                //sends response
                $myObj->res = "success";
                $JSON = json_encode($myObj);
                echo $JSON;
            } else {
                $myObj->res = "user error";
                $JSON = json_encode($myObj);
                echo $JSON;
            }
        } else {
            $myObj->res = "no post info";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
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

