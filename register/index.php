<?php
require ("../vendor/autoload.php");
require ("../aws/aws-autoloader.php");
use \Mailjet\Resources;

$myObj = (object)array();

// verifies it comes from authorized device
if (isset($_POST["auth"]) && $_POST["auth"] == "f82d371b7c8178f9632c83cb33bd3cfe4f8ae7847394a0ff3513f5d679ff5fb3"){
    require_once ("../connection.php");

    $type = mysqli_real_escape_string($conn, $_POST["type"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $last = mysqli_real_escape_string($conn, $_POST["last"]);
    $org = mysqli_real_escape_string($conn, $_POST["org"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $string = preg_replace('/\s+/','',$email);
    $email = strtolower($email);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $activation = 0;

    //verifies fields are not empty
    if (!empty($type) || !empty($name) || !empty($last) || !empty($org) || !empty($email) || !empty($password)){
        // verifies is a valid email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // verifies is a entrepreneur account
            if ($type == 1){
                //verifies email is not taken
                if (verify_email($email, $conn, 1)){
                    //hashing password
                    $conf = array('cost'=>8);
                    $password = $password.$email."entrepreneur";
                    $password = password_hash($password, PASSWORD_BCRYPT, $conf);

                    // creates user token
                    $token = $password.$email."entrepreneur";
                    $token = password_hash($token, PASSWORD_BCRYPT, $conf);

                    // inserts user into DB
                    $statement = $conn->prepare("INSERT INTO `user_entrepreneur`(`name`, `last_name`, `organization`, `email`, `password`, `activation`, `token`) VALUES (?,?,?,?,?,?,?)");
                    $statement->bind_param("sssssis", $name, $last, $org, $email, $password, $activation, $token);
                    $statement->execute();

                    // gets the id of the user
                    $getId = $conn->prepare("SELECT `id` FROM `user_entrepreneur` WHERE `name`=? AND `last_name`=? AND `organization`=? AND `email`=? AND `password`=?");
                    $getId->bind_param("sssss", $name, $last, $org, $email, $password);
                    $getId->execute();

                    $getResults = $getId->get_result();
                    if($getResults->num_rows > 0  && $getResults->num_rows < 2) {
                        $rowVal = $getResults->fetch_assoc();
                        $id = $rowVal["id"];

                        // creates s3 folder
                        try{
                            create_s3_bucket(1, $id);
                        } catch (Exception $e){

                        }
                    }

                    // sends welcome email to user
                    send_email($email, $name);

                    // encodes jason and send response
                    $myObj->res = "success";
                    $myObj->type = "1";
                    $myObj->token = $token;
                    $JSON = json_encode($myObj);
                    echo $JSON;
                } else {
                    $myObj->res = "taken";
                    $JSON = json_encode($myObj);
                    echo $JSON;
                }
            } elseif ($type == 2){ // investor account
                // verifies email is not taken
                if (verify_email($email, $conn, 2)){
                    // hashing password
                    $conf = array('cost'=>8);
                    $password = $password.$email."investor";
                    $password = password_hash($password, PASSWORD_BCRYPT, $conf);

                    // creates user token
                    $token = $password.$email."investor";
                    $token = password_hash($token, PASSWORD_BCRYPT, $conf);

                    // inserts user into DB
                    $statement = $conn->prepare("INSERT INTO `user_investor`(`name`, `last_name`, `organization`, `email`, `password`, `activation`, `token`) VALUES (?,?,?,?,?,?,?)");
                    $statement->bind_param("sssssis", $name, $last, $org, $email, $password, $activation, $token);
                    $statement->execute();

                    // get user id from DB
                    $getId = $conn->prepare("SELECT `id` FROM `user_investor` WHERE `name`=? AND `last_name`=? AND `organization`=? AND `email`=? AND `password`=?");
                    $getId->bind_param("sssss", $name, $last, $org, $email, $password);
                    $getId->execute();

                    $getResults = $getId->get_result();
                    if($getResults->num_rows > 0  && $getResults->num_rows < 2) {
                        $rowVal = $getResults->fetch_assoc();
                        $id = $rowVal["id"];

                        // creates s3 folder
                        try{
                            create_s3_bucket(2, $id);
                        } catch (Exception $e){

                        }
                    }

                    // send welcome email
                    send_email($email, $name);

                    // encodes json and send response
                    $myObj->res = "success";
                    $myObj->type = "2";
                    $myObj->token = $token;
                    $JSON = json_encode($myObj);
                    echo $JSON;
                } else {
                    $myObj->res = "taken";
                    $JSON = json_encode($myObj);
                    echo $JSON;
                }
            } else {
                $myObj->res = "fail";
                $JSON = json_encode($myObj);
                echo $JSON;
            }
        } else {
            $myObj->res = "fail";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "fail";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "no get";
    $JSON = json_encode($myObj);
    echo $JSON;
}

// send email to user with welcome message
function send_email($email, $name){
    // set mailjet credentials
    $mj = new \Mailjet\Client('fe5ff2652b29d928de4ea0852d57aa6f','55951c0c7976fedadeec2c7c6bc3140c',true,['version' => 'v3.1']);

    // constructs email body
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
                    ]
                ],
                'Subject' => "Bienvenido",
                'TextPart' => "Bienvenido",
                'HTMLPart' => "<body style='width: 600px; margin: 0; padding: 0; background-color: whitesmoke'>
                                    <div class='header' style='width: 100%; background-color: white' align='center'>
                                        <img src='https://vventuregeneral.s3.us-east-2.amazonaws.com/vventure-logo.png' style='height: 70px' alt='VVENTURE'>
                                    </div>
                                    <div style='width: 100%; background-color: white; margin-top: 25px' align='center'>
                                        <div style='padding-top: 35px; width: 100%'>
                                            <table>
                                                <tr align='center'>
                                                    <td style=\"font-family: 'Calibri Light'; font-size: 24px; font-weight: bold\" align='center'>
                                                    Hola
                                                    </td>
                                                </tr>
                                                <tr align='center'>
                                                    <td style=\"font-family: 'Calibri Light'; font-size: 18px; font-weight: bold\" align='center'>
                                                        ".$name."
                                                    </td>
                                                </tr>
                                                <tr align='center'>
                                                    <td style=\"font-family: 'Calibri Light'; font-size: 24px; font-weight: bold; padding-top: 25px\" align='center'>
                                                    Te damos la Bienvenida a
                                                    </td>
                                                </tr>
                                                <tr align='center'>
                                                    <td style=\"font-family: 'Calibri Light'; font-size: 18px; font-weight: lighter; padding-top: 25px\" align='center'>
                                                        VVENTURE
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div style='padding-bottom: 50px'></div>
                                    </div>
                                    
                                    <div style='width: 100%; margin-top: 25px; background-color: white; padding: 25px 0 25px 0' align='center'>
                                        <div align='center' style=\"font-family: 'Calibri Light'; font-size: 18px; font-weight: lighter\">
                                            Gracias Por Preferirnos
                                        </div>
                                    </div>
                                    
                                    </body>
                                    ",
            ]
        ]
    ];

    //sends verification email
    $mj->post(Resources::$Email, ['body' => $body]);
}

// verifies the email address is not taken
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

// creates an empty s3 bucket folder with the id of the user and makes it public
function create_s3_bucket($type, $id){
    $bucket_set = false;

    if ($type == 1){
        $bucketName = 'vventureent';
        $bucket_set = true;
    } elseif ($type == 2){
        $bucketName = 'vventureinv';
        $bucket_set = true;
    }

    if ($bucket_set == true){
        $IAM_KEY = 'AKIAJ6VDWA3J2OM5L7WA';
        $IAM_SECRET = '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';

        try {
            $s3 = new Aws\S3\S3Client([ // Set Amazon S3 Credentials
                'version' => 'latest',
                'region'  => 'us-east-2',
                'credentials' => [
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET,
                ]
            ]);
        } catch (Exception $e) {
            die();
        }

        try {
            $s3->putObject(array( // create empty folder in s3
                'Bucket' => $bucketName,
                'Key'    => $id."/",
                'Body'   => "",
                'ACL'    => 'public-read'
            ));
        } catch (Exception $e) {
            die();
        }
    }
}
