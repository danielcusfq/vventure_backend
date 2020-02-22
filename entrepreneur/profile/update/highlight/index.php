<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == "5a4517b3a15a2fc8961e5aeb63af6663f0cdcd9c1e48183dd67e57f6d7fb3728") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['detail']) && isset($_POST['id_highlight']) && !empty($_POST['detail']) && !empty($_POST['id_highlight'])) {
            require("../../../../connection.php");
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $description = $_POST['detail'];
            $id_highlight = $_POST['id_highlight'];

            if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                $updateStatement = $conn->prepare("UPDATE `highlights_entrepreneur` SET `description`=? WHERE id_entrepreneur=? AND id_highlight=?");
                $updateStatement->bind_param("sii", $description, $id, $id_highlight);
                $updateStatement->execute();

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

