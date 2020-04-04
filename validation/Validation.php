<?php
class Validation{
    //verifies if user exists on database
    static function VerifyUser($id, $type, $token, $conn){
        $activation = 1;

        if ($type == 1){
            $validationStmt = $conn->prepare("SELECT `id` FROM `user_entrepreneur` WHERE `id`=? AND `token`=? AND `activation`=?");
        } elseif ($type == 2){
            $validationStmt = $conn->prepare("SELECT `id` FROM `user_investor` WHERE `id`=? AND `token`=? AND `activation`=? ");
        } else {
            return false;
        }

        $validationStmt->bind_param("isi", $id, $token, $activation);
        $validationStmt->execute();
        $validationResults = $validationStmt->get_result();

        if ($validationResults->num_rows == 1){
            return true;
        } else {
            return false;
        }
    }

    //verifies if user is in favorites
    static function IsInFavorites($id, $counterpart, $type, $conn){
        if ($type == 1){
            $validationStmt = $conn->prepare("SELECT `id_entrepreneur` FROM `favorites_entrepreneur` WHERE `id_entrepreneur`=? AND `id_investor`=? ");
        } elseif ($type == 2){
            $validationStmt = $conn->prepare("SELECT `id_investor` FROM `favorites_investor` WHERE `id_investor`=? AND `id_entrepreneur`=? ");
        } else {
            return "error";
        }

        $validationStmt->bind_param("ii", $id, $counterpart);
        $validationStmt->execute();
        $validationResults = $validationStmt->get_result();

        if ($validationResults->num_rows == 1){
            return true;
        } else {
            return false;
        }
    }
}
