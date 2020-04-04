<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == '2b7b9f856c3ce030f7545c3489b31c0687674208512e113a5d93a48cba0503db' && $_POST['type'] == 1) {
    if (isset($_POST['id_highlight']) && !empty($_POST['id_highlight'])) {
        require("../../../../connection.php");
        //gets data
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_highlight = $_POST['id_highlight'];

        //validates user
        if (Validation::VerifyUser($id, $type, $token, $conn) == true){
            //prepares query
            $updateStatement = $conn->prepare("DELETE FROM `highlights_entrepreneur` WHERE id_entrepreneur=? AND id_highlight=?");
            $updateStatement->bind_param("ii",  $id, $id_highlight);
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
