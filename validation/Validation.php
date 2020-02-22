<?php
class Validation{
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
}