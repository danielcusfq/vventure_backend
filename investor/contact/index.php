<?php
require ("../../vendor/autoload.php");
require_once('../../validation/Validation.php');
use \Mailjet\Resources;
$myObj = (object)array();

if (isset($_GET['auth']) && isset($_GET['id']) && isset($_GET['token']) && isset($_GET['entrepreneur']) && !empty($_GET['id'])
    && !empty($_GET['token']) && !empty($_GET['entrepreneur']) && $_GET['auth'] == "357c4b87c630bd41efc01097ff535209d1eba8bc536964902cf4e1653596ebbf"){
    require_once ('../../connection.php');
    $id = $_GET['id'];
    $token = $_GET['token'];
    $entrepreneur = $_GET['entrepreneur'];
    $type = 2;


    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        $getEmail = $conn->prepare("SELECT user_investor.email FROM user_investor WHERE user_investor.id=? AND user_investor.token=?");
        $getEmail->bind_param("is", $id, $token);
        $getEmail->execute();

        $getEmailResult = $getEmail->get_result();


        if ($getEmailResult->num_rows == 1){
            $emailRow = $getEmailResult->fetch_assoc();
            $investorEmail = $emailRow['email'];

            if (!empty($investorEmail)){
                $getUserInfo = $conn->prepare("SELECT user_entrepreneur.name, user_entrepreneur.last_name, user_entrepreneur.organization, 
                        user_entrepreneur.email, profile_entrepreneur.profile_picture FROM user_entrepreneur JOIN profile_entrepreneur WHERE 
                        user_entrepreneur.id=profile_entrepreneur.id_entrepreneur AND user_entrepreneur.id=?");
                $getUserInfo->bind_param("i", $entrepreneur);
                $getUserInfo->execute();
                $getUserInfoResult = $getUserInfo->get_result();

                if ($getUserInfoResult->num_rows > 0){
                    $row = $getUserInfoResult->fetch_assoc();
                    $pic = $row['profile_picture'];
                    $organization = $row['organization'];
                    $name = $row['name'] . " " . $row['last_name'];
                    $email = $row['email'];


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
                                        'Email' => "$investorEmail",
                                    ]
                                ],
                                'Subject' => "Información de Contacto",
                                'TextPart' => "Información de Contacto",
                                'HTMLPart' => "<body style='width: 600px; margin: 0; padding: 0; background-color: whitesmoke'>
                                                    <div class='header' style='width: 100%; background-color: white' align='center'>
                                                        <img src='https://vventuregeneral.s3.us-east-2.amazonaws.com/vventure-logo.png' style='height: 70px' alt='VVENTURE'>
                                                    </div>
                                                    <div style='width: 100%; background-color: white; margin-top: 25px' align='center'>
                                                        <div style='padding-top: 50px; width: 100%'>
                                                            <img src='".$pic."' alt='".$organization."' width='125' height='125'
                                                                 style='border-radius:
                                                        50%'>
                                                        </div>
                                                        <div style='padding-top: 5px; width: 100%' align='center'>
                                                            <table style='width: 100%' align='center'>
                                                                <tr align='center'>
                                                                    <td style=\"font-family: 'Calibri Light'; font-size: 24px; font-weight: bold\" align='center'>
                                                                        ".$organization."
                                                                    </td>
                                                                </tr>
                                                                <tr align='center'>
                                                                    <td style=\"font-family: 'Calibri Light'; font-size: 18px; font-weight: lighter; padding-top: 25px\" align='center'>
                                                                        Run by
                                                                    </td>
                                                                </tr>
                                                                <tr align='center'>
                                                                    <td style=\"font-family: 'Calibri Light'; font-size: 24px; font-weight: bold\" align='center'>
                                                                        ".$name."
                                                                    </td>
                                                                </tr>
                                                                <tr align='center'>
                                                                    <td style=\"font-family: 'Calibri Light'; font-size: 18px; font-weight: lighter; padding-top: 25px\" align='center'>
                                                                        Contact Email
                                                                    </td>
                                                                </tr>
                                                                <tr align='center'>
                                                                    <td style=\"font-family: 'Calibri Light'; font-size: 24px; font-weight: bold\" align='center'>
                                                                        ".$email."
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div style='padding-bottom: 50px'></div>
                                                    </div>
                                                    
                                                    <div style='width: 100%; margin-top: 25px; background-color: white; padding: 25px 0 25px 0' align='center'>
                                                        <div align='center' style=\"font-family: 'Calibri Light'; font-size: 18px; font-weight: lighter\">
                                                            Gracias Por Utilizar VVENTURE
                                                        </div>
                                                    </div>
                                                    
                                                    </body>
                                                ",
                            ]
                        ]
                    ];
                    $mj->post(Resources::$Email, ['body' => $body]);

                    $myObj->res = "success";
                    $JSON = json_encode($myObj);
                    echo $JSON;
                } else {
                    $myObj->res = "error no user";
                    $JSON = json_encode($myObj);
                    echo $JSON;
                }
            } else {
                $myObj->res = "error no email";
                $JSON = json_encode($myObj);
                echo $JSON;
            }
        } else {
            $myObj->res = "error no user";
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
