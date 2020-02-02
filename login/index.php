<?php
$myObj = (object)array();

if (isset($_POST["ok"]) && $_POST["ok"] == "ok"){
    require_once ("../connection.php");

    $type = mysqli_real_escape_string($conn, $_POST["type"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $email = $string = preg_replace('/\s+/','',$email);
    $email = strtolower($email);
    $password = $_POST["password"];

    if ($type == 1){
        $password = $password.$email."entrepreneur";
        $user = verify_user($conn, 1, $email, $password);
        if ($user->get_token() != null || $user->get_activation() != null){
            $token = $user->get_token();
            $activation = $user->get_activation();

            $myObj->res = "success";
            $myObj->type = "1";
            $myObj->token = $token;
            $myObj->activation = $activation;
            $JSON = json_encode($myObj);
            echo $JSON;
        } else {
            $myObj->res = "error no user info";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else if ($type == 2){
        $password = $password.$email."investor";
        $user = verify_user($conn, 2, $email, $password);
        if ($user->get_token() != null || $user->get_activation() != null){
            $token = $user->get_token();
            $activation = $user->get_activation();

            $myObj->res = "success";
            $myObj->type = "2";
            $myObj->token = $token;
            $myObj->activation = $activation;
            $JSON = json_encode($myObj);
            echo $JSON;
        } else {
            $myObj->res = "error no user info";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "error no type";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no get";
    $JSON = json_encode($myObj);
    echo $JSON;
}

function verify_user($conn, $type, $email, $password){
    $validQuery = true;

    if ($type == 1){
        $validationStmt = $conn->prepare("SELECT `id`, `password`, `activation`, `token` FROM `user_entrepreneur` WHERE `email`=? ");
    } elseif ($type == 2){
        $validationStmt = $conn->prepare("SELECT `id`, `password`, `activation`, `token` FROM `user_investor` WHERE `email`=? ");
    } else {
        $validQuery = false;
    }

    if ($validQuery == true){
        $validationStmt->bind_param("s", $email);
        $validationStmt->execute();
        $validationResult = $validationStmt->get_result();

        if($validationResult->num_rows > 0  && $validationResult->num_rows < 2) {
            $rowVal = $validationResult->fetch_assoc();
            if(password_verify($password, $rowVal["password"])) {
                $auth_info = new Auth_Info($rowVal["token"], $rowVal["activation"]);
                return $auth_info;
            } else {
                return new Auth_Info(null, null);
            }
        }
        else {
            return new Auth_Info(null, null);
        }
    } else {
        return new Auth_Info(null, null);
    }
}

class Auth_Info {
    private $token;
    private $activation;

    public function __construct($token, $activation){
        $this->set_token($token);
        $this->set_activation($activation);
    }

    function set_token($token) {
        $this->token = $token;
    }
    function get_token() {
        return $this->token;
    }

    function set_activation($activation) {
        $this->activation = $activation;
    }
    function get_activation() {
        return $this->activation;
    }
}