<?php
require ("../../aws/aws-autoloader.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$myObj = (object)array();

// verifies it comes from authorized device
if (isset($_POST["auth"]) && $_POST["auth"] == "a5d2f6ffbaeb6e229e05e0b2e6a9136473778c0160d3a4d07f4c380067b3c2cd"){
    require ("../../connection.php");
    //data init
    $token = $_POST["token"];
    $type = mysqli_real_escape_string($conn,$_POST["type"]);
    $image = "";
    $interest = mysqli_real_escape_string($conn,$_POST["interests"]);
    $background = mysqli_real_escape_string($conn,$_POST["background"]);
    $video = "";
    $activation = 1;

    if (isset($_POST["token"]) && isset($_POST["type"]) && $type == "2"){
        // authenticate user and returns id
        $id = auth_user($token, $type, $conn);
        if (!empty($interest) && !empty($background)){
            if ($id != false){
                if (isset($_POST["image"]) && !empty($_POST["image"])){
                    // upload image to aws s3
                    $image = uploadImage($id);
                } else {
                    $image = "https://vventuregeneral.s3.us-east-2.amazonaws.com/empty_profile.png";
                }

                // inserts basic profile info to DB
                $insertStatement = $conn->prepare("INSERT INTO `profile_investor`(`id_investor`, `profile_picture`, `profile_video`, `interests`, `background`) VALUES (?,?,?,?,?)");
                $insertStatement->bind_param("issss", $id, $image, $video, $interest, $background);
                $insertStatement->execute();

                // updates activation value to 1 from user
                $uploadStatement = $conn->prepare("UPDATE `user_investor` SET `activation`=? WHERE `id`=? AND `token`=? ");
                $uploadStatement->bind_param("iis", $activation, $id, $token);
                $uploadStatement->execute();

                // obtains all data to form the json response
                $authInfo = $conn->prepare("SELECT `token`, `activation` FROM `user_investor` WHERE `id`=? AND `token`=?");
                $authInfo->bind_param("is", $id, $token);
                $authInfo->execute();
                $authResults = $authInfo->get_result();

                // verifies we get a row with the results and send json back
                if ($authResults->num_rows == 1) {
                    $row = $authResults->fetch_assoc();
                    $token = $row["token"];
                    $activation = $row["activation"];

                    $myObj->res = "success";
                    $myObj->id = $id;
                    $myObj->type = "2";
                    $myObj->token = $token;
                    $myObj->activation = $activation;
                    $JSON = json_encode($myObj);
                    echo $JSON;
                } else {
                    $myObj->res = "User Authentication Error";
                    $JSON = json_encode($myObj);
                    echo $JSON;
                }
            } else {
                $myObj->res = "User Authentication Error";
                $JSON = json_encode($myObj);
                echo $JSON;
            }
        } else {
            $myObj->res = "empty";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "Type Error";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "No Auth Token";
    $JSON = json_encode($myObj);
    echo $JSON;
}

// authenticate user and returns id
function auth_user($token, $type, $conn){
    if ($type == 2){
        $validationStmt = $conn->prepare("SELECT `id` FROM `user_investor` WHERE `token`=?");
    } else {
        return false;
    }

    $validationStmt->bind_param("s", $token);
    $validationStmt->execute();
    $validationResult = $validationStmt->get_result();

    if($validationResult->num_rows == 1) {
        $rowVal = $validationResult->fetch_assoc();
        $validEmail = $rowVal["id"];
    }
    else {
        $validEmail = false;
    }

    return $validEmail;
}

// upload an image to aws s3 bucket
function uploadImage($id){
    $empty = "https://vventuregeneral.s3.us-east-2.amazonaws.com/empty_profile.png";
    if (isset($_POST['image']) && !empty($_POST['image'])){
        $bucketName = 'vventureinv';
        $IAM_KEY = 'AKIAJ6VDWA3J2OM5L7WA';
        $IAM_SECRET = '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';

        $image = base64_decode($_POST['image']);
        $extension = $_POST['ext'];
        $name = $id."ProfilePic.".$extension;
        file_put_contents($name, $image);
        chmod("/var/www/html/complete_register/investor/".$name, 0755);
        $file = "/var/www/html/complete_register/investor/".$name;

        // Set Amazon S3 Credentials
        try {
            $s3 = new Aws\S3\S3Client([
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

        // Allow certain file formats
        if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "gif" ) {
            return $empty;
        }

        $fileKey = $id."/ProfilePic.".$extension;

        //Image Upload to S3 bucket
        try {
            $s3->putObject(
                array(
                    'Bucket'=>$bucketName,
                    'Key' =>  $fileKey,
                    'SourceFile' => $file,
                    'StorageClass' => 'STANDARD',
                    'ACL'    => 'public-read'
                )
            );

            $url = $s3->getObjectUrl($bucketName, $fileKey);
        } catch (Exception $e) {
            die();
        }

        @unlink("/var/www/html/complete_register/investor/".$name);
        clearstatcache();

        return $url;
    } else {
        return $empty;
    }
}
