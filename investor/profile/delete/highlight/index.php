<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == 'c563b7856ac22839f792721ac02d294c633fe9544e7081e727242b830d58fe6b' && $_POST['type'] == 2) {
    if (isset($_POST['id_highlight']) && !empty($_POST['id_highlight'])) {
        require("../../../../connection.php");
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_highlight = $_POST['id_highlight'];

        if (Validation::VerifyUser($id, $type, $token, $conn) == true){
            $updateStatement = $conn->prepare("DELETE FROM `highlights_investor` WHERE id_investor=? AND id_highlights=?");
            $updateStatement->bind_param("ii",  $id, $id_highlight);
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
