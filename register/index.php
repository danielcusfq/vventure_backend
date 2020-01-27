<?php
require ("../vendor/autoload.php");
use \Mailjet\Resources;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET["ok"]) && $_GET["ok"] == "ok"){
    require_once ("../connection.php");

    $type = mysqli_real_escape_string($conn, $_GET["type"]);
    $name = mysqli_real_escape_string($conn, $_GET["name"]);
    $last = mysqli_real_escape_string($conn, $_GET["last"]);
    $org = mysqli_real_escape_string($conn, $_GET["org"]);
    $email = mysqli_real_escape_string($conn, $_GET["email"]);
    $string = preg_replace('/\s+/','',$email);
    $email = strtolower($email);
    $password = mysqli_real_escape_string($conn, $_GET["password"]);
    $activation = 0;

    if (!empty($type) || !empty($name) || !empty($last) || !empty($org) || !empty($email) || !empty($password)){
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($type == 1){ // entrepreneur account
                if (verify_email($email, $conn, 1)){
                    $conf = array('cost'=>8);
                    $password = $password.$email."entrepreneur";
                    $password = password_hash($password, PASSWORD_BCRYPT, $conf);

                    $token = $password.$email."entrepreneur";
                    $token = password_hash($token, PASSWORD_BCRYPT, $conf);

                    $statement = $conn->prepare("INSERT INTO `user_entrepreneur`(`name`, `last_name`, `organization`, `email`, `password`, `activation`, `token`) VALUES (?,?,?,?,?,?,?)");
                    $statement->bind_param("sssssis", $name, $last, $org, $email, $password, $activation, $token);
                    $statement->execute();

                    send_email($email, $name);

                    $myObj = array();
                    $myObj->res = "success";
                    $JSON = json_encode($myObj);
                } else {
                    $myObj = array();
                    $myObj->res = "taken";
                    $JSON = json_encode($myObj);
                }
            } elseif ($type == 2){ // investor account
                if (verify_email($email, $conn, 2)){
                    $conf = array('cost'=>8);
                    $password = $password.$email."investor";
                    $password = password_hash($password, PASSWORD_BCRYPT, $conf);

                    $token = $password.$email."investor";
                    $token = password_hash($token, PASSWORD_BCRYPT, $conf);

                    $statement = $conn->prepare("INSERT INTO `user_investor`(`name`, `last_name`, `organization`, `email`, `password`, `activation`, `token`) VALUES (?,?,?,?,?,?,?)");
                    $statement->bind_param("sssssis", $name, $last, $org, $email, $password, $activation, $token);
                    $statement->execute();

                    send_email($email, $name);

                    $myObj = array();
                    $myObj->res = "success";
                    $JSON = json_encode($myObj);
                } else {
                    $myObj = array();
                    $myObj->res = "taken";
                    $JSON = json_encode($myObj);
                }
            } else {
                echo "fail";;
            }
        } else {
            echo("fail");
        }
    }
}

function send_email($email, $name){
    $mj = new \Mailjet\Client('fe5ff2652b29d928de4ea0852d57aa6f','55951c0c7976fedadeec2c7c6bc3140c',true,['version' => 'v3.1']);

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "do-not-reply@vventure.tk",
                    'Name' => "DO-NOT-REPLY"
                ],
                'To' => [
                    [
                        'Email' => "$email",
                        'Name' => "Daniel Cabrera"
                    ]
                ],
                'Subject' => "Bienvenido",
                'TextPart' => "Bienvenido",
                'HTMLPart' => "Hola $name <br> Te damos la vienbenida a vventure",
            ]
        ]
    ];

    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success() && var_dump($response->getData());
}

function verify_email($email, $conn, $type){
    if ($type == 1){
        $validationStmt = $conn->prepare("SELECT `id` FROM `user_entrepreneur` WHERE `email`=?");
    } elseif ($type == 2){
        $validationStmt = $conn->prepare("SELECT `id` FROM `user_investor` WHERE `email`=?");
    } else {
        return false;
    }

    $validationStmt->bind_param("s", $email);
    $validationStmt->execute();
    $validationResult = $validationStmt->get_result();

    if($validationResult->num_rows > 0) {
        $validEmail = false;
    }
    else {
        $validEmail = true;
    }

    return $validEmail;
}