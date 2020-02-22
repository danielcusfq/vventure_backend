<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == 'eb432260e66deef8a6482ae9cebf98f5faabbcc0f19ce08b5edeb1bbdd043457' && $_POST['type'] == 1) {
    if (isset($_POST['id_image']) && !empty($_POST['id_image'])) {
        require("../../../../connection.php");
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_image = $_POST['id_image'];

        if (Validation::VerifyUser($id, $type, $token, $conn) == true){
            $updateStatement = $conn->prepare("DELETE FROM `images_entrepreneur` WHERE id_entrepreneur=? AND id_image=?");
            $updateStatement->bind_param("ii",  $id, $id_image);
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