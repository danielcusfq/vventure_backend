<?php
require ("../../aws/aws-autoloader.php");

$myObj = (object)array();

// verifies it comes from authorized device
if (isset($_POST["auth"]) && $_POST["auth"] == "b2df705644a0c7ff7dd469afa096c56d6da918cfcf827d69631dcacfccf54fa5"){
    require ("../../connection.php");

    $token = $_POST["token"];
    $type = mysqli_real_escape_string($conn,$_POST["type"]);
    $image = "";
    $stage = mysqli_real_escape_string($conn,$_POST["stage"]);
    $percentage = mysqli_real_escape_string($conn,$_POST["percentage"]);
    $exchange = mysqli_real_escape_string($conn,$_POST["exchange"]);
    $problem = mysqli_real_escape_string($conn,$_POST["problem"]);
    $solution = mysqli_real_escape_string($conn,$_POST["solution"]);
    $video = "";
    $activation = 1;

    if (isset($_POST["token"]) && isset($_POST["type"]) && $type == "1"){
        // authenticate user and returns id
        $id = auth_user($token, $type, $conn);

        if ($id != false){
            if (isset($_POST["image"]) && !empty($_POST["image"])){
                // upload image to aws s3
                $image = uploadImage($id);
            } else {
                $image = "https://vventuregeneral.s3.us-east-2.amazonaws.com/empty_profile.png";
            }

            // inserts basic profile info to DB
            $insertStatement = $conn->prepare("INSERT INTO `profile_entrepreneur`(`id_entrepreneur`, `stage`, `stake`, `stake_info`, `problem`, `solution`, `profile_picture`, `profile_video`) VALUES (?,?,?,?,?,?,?,?)");
            $insertStatement->bind_param("isisssss", $id, $stage, $percentage, $exchange, $problem, $solution, $image, $video);
            $insertStatement->execute();

            // updates activation value to 1 from user
            $uploadStatement = $conn->prepare("UPDATE `user_entrepreneur` SET `activation`=? WHERE `id`=? AND `token`=? ");
            $uploadStatement->bind_param("iis", $activation, $id, $token);
            $uploadStatement->execute();

            // obtains all data to form the json response
            $authInfo = $conn->prepare("SELECT `token`, `activation` FROM `user_entrepreneur` WHERE `id`=? AND `token`=?");
            $authInfo->bind_param("is", $id, $token);
            $authInfo->execute();
            $authResults = $authInfo->get_result();

            // verifies we get a row with the results and send json back
            if ($authResults->num_rows == 1) {
                $row = $authResults->fetch_assoc();
                $token = $row["token"];
                $activation = $row["activation"];

                $myObj->res = "success";
                $myObj->type = "1";
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
    if ($type == 1){
        $validationStmt = $conn->prepare("SELECT `id` FROM `user_entrepreneur` WHERE `token`=?");
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
        $bucketName = 'vventureent';
        $IAM_KEY = 'AKIAJ6VDWA3J2OM5L7WA';
        $IAM_SECRET = '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';

        $image = base64_decode($_POST['image']);
        $extension = $_POST['ext'];
        $name = $id."ProfilePic.".$extension;
        file_put_contents($name, $image);
        chmod("/var/www/html/complete_register/entrepreneur/".$name, 0755);
        $file = "/var/www/html/complete_register/entrepreneur/".$name;

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

        @unlink("/var/www/html/complete_register/entrepreneur/".$name);
        clearstatcache();

        return $url;
    } else {
        return $empty;
    }
}