<?php
require_once('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "58c9f66f088872805a34ebbe24f971f80b6e914736d08a4fedcdcdfb743c3c9b") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['image']) && !empty($_POST['image']) && isset($_POST['ext']) && !empty($_POST['ext'])) {
            require("../../../../connection.php");
            require("../../../../aws/aws-autoloader.php");
            $bucketName = 'vventureent';
            $IAM_KEY = 'AKIAJ6VDWA3J2OM5L7WA';
            $IAM_SECRET = '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';
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $image = base64_decode($_POST['image']);
            $extension = $_POST['ext'];
            $extension = strtolower($extension);
            $salt = substr(sha1($id . date("h:i:sa") . "entrepreneur"), 15);
            $name = $id . "ProfileImage" . $salt . "." . $extension;

            // Allow certain file formats
            if ($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "gif") {
                $myObj->res = "error file type";
                $JSON = json_encode($myObj);
                echo $JSON;
            } else {
                if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
                    $selectURL = $conn->prepare("SELECT `profile_picture` FROM profile_entrepreneur WHERE id_entrepreneur=?");
                    $selectURL->bind_param("i", $id);
                    $selectURL->execute();
                    $selectURLResults = $selectURL->get_result();
                    if ($selectURLResults->num_rows == 1) {
                        $row = $selectURLResults->fetch_assoc();
                        $path = $row['profile_picture'];

                        $fileKey = @str_replace("https://vventureent.s3.us-east-2.amazonaws.com/","",$path);
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

                        try {
                            $s3->deleteObject(
                                array(
                                    'Bucket'=>$bucketName,
                                    'Key' =>  $fileKey,
                                )
                            );
                        } catch (Exception $e) {

                        }

                        @file_put_contents($name, $image);
                        $file = "/var/www/html/entrepreneur/profile/update/profile_image/" . $name;
                        @chmod($file, 0777);
                        $fileKey = $id . "/ProfileImage." . $extension;

                        //Image Upload to S3 bucket
                        try {
                            $s3->putObject(
                                array(
                                    'Bucket' => $bucketName,
                                    'Key' => $fileKey,
                                    'SourceFile' => $file,
                                    'StorageClass' => 'STANDARD',
                                    'ACL' => 'public-read'
                                )
                            );

                            $url = $s3->getObjectUrl($bucketName, $fileKey);
                        } catch (Exception $e) {
                            die();
                        }

                        @unlink($file);
                        clearstatcache();

                        if (!empty($url)) {
                            $updateStmt = $conn->prepare("UPDATE profile_entrepreneur SET `profile_picture`=? WHERE id_entrepreneur=?");
                            $updateStmt->bind_param("si", $url, $id);
                            $updateStmt->execute();

                            $myObj->res = "success";
                            $JSON = json_encode($myObj);
                            echo $JSON;
                        } else {
                            $url = "https://vventuregeneral.s3.us-east-2.amazonaws.com/empty_profile.png";
                            $updateStmt = $conn->prepare("UPDATE profile_entrepreneur SET `profile_video`=? WHERE id_entrepreneur=?");
                            $updateStmt->bind_param("si", $url, $id);
                            $updateStmt->execute();

                            $myObj->res = "server error";
                            $JSON = json_encode($myObj);
                            echo $JSON;
                        }
                    } else {
                        $myObj->res = "user error";
                        $JSON = json_encode($myObj);
                        echo $JSON;
                    }
                } else {
                    $myObj->res = "invalid user";
                    $JSON = json_encode($myObj);
                    echo $JSON;
                }
            }
        } else {
            $myObj->res = "error with image data";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "error no user info";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no auth info";
    $JSON = json_encode($myObj);
    echo $JSON;
}
