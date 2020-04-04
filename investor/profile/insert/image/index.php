<?php
require_once ('../../../../validation/Validation.php');
$myObj = (object)array();

if (isset($_POST["auth"]) && $_POST["auth"] == "c0cdcf6c5d22053916a5efcdcb23fae308212585740a758088f10be4e2df2808") {
    if (isset($_POST['id']) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST['id']) && !empty
        ($_POST['token']) && $_POST['type'] == 2) {
        if (isset($_POST['image']) && !empty($_POST['image']) && isset($_POST['ext']) && !empty($_POST['ext'])) {
            require ("../../../../connection.php");
            require ("../../../../aws/aws-autoloader.php");
            //gets data
            $bucketName = 'vventureinv';
            $IAM_KEY = 'AKIAJ6VDWA3J2OM5L7WA';
            $IAM_SECRET = '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';
            $id = $_POST['id'];
            $type = $_POST['type'];
            $token = $_POST['token'];
            $image = base64_decode($_POST['image']);
            $extension = $_POST['ext'];
            $extension = strtolower($extension);
            $salt = substr(sha1($id.date("h:i:sa")."investor"), 15);
            $name = $id."WorkImage".$salt.".".$extension;

            // Allow certain file formats
            if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "gif" ) {
                $myObj->res = "error file type";
                $JSON = json_encode($myObj);
                echo $JSON;
            } else {
                //validates user
                if (Validation::VerifyUser($id, $type, $token, $conn) == true){
                    //put temp file on server
                    file_put_contents($name, $image);
                    chmod("/var/www/html/investor/profile/insert/image/".$name, 0777);
                    $file = "/var/www/html/investor/profile/insert/image/".$name;

                    // Set Amazon S3 Credentials and creates instance
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

                    //generates file key
                    $fileKey = $id."/WorkImage".$salt.".".$extension;

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

                    //deletes temp file from server
                    @unlink("/var/www/html/investor/profile/insert/image/".$name);
                    clearstatcache();

                    if (!empty($url)){
                        //updates db
                        $insertStmt = $conn->prepare("INSERT INTO `images_investor` (`id_investor`, `image_path`) VALUES (?,?)");
                        $insertStmt->bind_param("is", $id, $url);
                        $insertStmt->execute();

                        //sends response
                        $myObj->res = "success";
                        $JSON = json_encode($myObj);
                        echo $JSON;
                    } else {
                        $myObj->res = "server error";
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
    } else{
        $myObj->res = "error no user info";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no auth info";
    $JSON = json_encode($myObj);
    echo $JSON;
}
