<?php
require_once('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "2d75b3c9f1a0986361022cc789546001ca5370224cda732e55aa18c3c0549867") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty($_POST['token']) && $_POST['type'] == 1) {
        if (isset($_POST['video']) && !empty($_POST['video']) && isset($_POST['ext']) && !empty($_POST['ext'])) {
            require("../../../../connection.php");
            require("../../../../aws/aws-autoloader.php");
            $bucketName = 'vventureent';
            $IAM_KEY = 'AKIAJ6VDWA3J2OM5L7WA';
            $IAM_SECRET = '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';
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $video = base64_decode($_POST['video']);
            $extension = $_POST['ext'];
            $salt = substr(sha1($id . date("h:i:sa") . "entrepreneur"), 15);
            $name = $id . "ProfileVideo" . $salt . "." . $extension;

            // Allow certain file formats
            if ($extension != "mp4" && $extension != "mov" && $extension != "webm" && $extension != "ogg") {
                $myObj->res = "error file type";
                $JSON = json_encode($myObj);
                echo $JSON;
            } else {
                if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
                    $selectURL = $conn->prepare("SELECT `profile_video` FROM profile_entrepreneur WHERE id_entrepreneur=?");
                    $selectURL->bind_param("i", $id);
                    $selectURL->execute();
                    $selectURLResults = $selectURL->get_result();
                    if ($selectURLResults->num_rows == 1) {
                        $row = $selectURLResults->fetch_assoc();
                        $path = $row['profile_video'];
                        if ($path == "https://vventuregeneral.s3.us-east-2.amazonaws.com/empty_video.mp4"){
                            @file_put_contents($name, $video);
                            $file = "/var/www/html/entrepreneur/profile/update/video/" . $name;
                            @chmod($file, 0777);
                            $fileKey = $id . "/ProfileVideo." . $extension;

                            // Set Amazon S3 Credentials
                            try {
                                $s3 = new Aws\S3\S3Client([
                                    'version' => 'latest',
                                    'region' => 'us-east-2',
                                    'credentials' => [
                                        'key' => $IAM_KEY,
                                        'secret' => $IAM_SECRET,
                                    ]
                                ]);
                            } catch (Exception $e) {
                                die();
                            }

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
                                $updateStmt = $conn->prepare("UPDATE profile_entrepreneur SET `profile_video`=? WHERE id_entrepreneur=?");
                                $updateStmt->bind_param("si", $url, $id);
                                $updateStmt->execute();

                                $myObj->res = "success";
                                $JSON = json_encode($myObj);
                                echo $JSON;
                            } else {
                                $url = "https://vventuregeneral.s3.us-east-2.amazonaws.com/empty_video.mp4";
                                $updateStmt = $conn->prepare("UPDATE profile_entrepreneur SET `profile_video`=? WHERE id_entrepreneur=?");
                                $updateStmt->bind_param("si", $url, $id);
                                $updateStmt->execute();

                                $myObj->res = "server error";
                                $JSON = json_encode($myObj);
                                echo $JSON;
                            }
                        } else {
                            $fileKey = str_replace("https://vventureent.s3.us-east-2.amazonaws.com/","",$path);
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
                                die();
                            }

                            @file_put_contents($name, $video);
                            $file = "/var/www/html/entrepreneur/profile/update/video/" . $name;
                            @chmod($file, 0777);
                            $fileKey = $id . "/ProfileVideo." . $extension;

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
                                $updateStmt = $conn->prepare("UPDATE profile_entrepreneur SET `profile_video`=? WHERE id_entrepreneur=?");
                                $updateStmt->bind_param("si", $url, $id);
                                $updateStmt->execute();

                                $myObj->res = "success";
                                $JSON = json_encode($myObj);
                                echo $JSON;
                            } else {
                                $url = "https://vventuregeneral.s3.us-east-2.amazonaws.com/empty_video.mp4";
                                $updateStmt = $conn->prepare("UPDATE profile_entrepreneur SET `profile_video`=? WHERE id_entrepreneur=?");
                                $updateStmt->bind_param("si", $url, $id);
                                $updateStmt->execute();

                                $myObj->res = "server error";
                                $JSON = json_encode($myObj);
                                echo $JSON;
                            }
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
