<?php
require('../../../../validation/Validation.php');
require ("../../../../aws/aws-autoloader.php");
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == '08afd9c6759ef1f08f9a03cfb23bc8d1e0b6b7f6faf4012342f7fe22ad815dd9' && $_POST['type'] == 2) {
    if (isset($_POST['id_image']) && !empty($_POST['id_image'])) {
        require("../../../../connection.php");
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_image = $_POST['id_image'];

        if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
            $selectURL = $conn->prepare("SELECT `image_path` FROM images_investor WHERE id_investor=? AND id_image=?");
            $selectURL->bind_param("ii", $id, $id_image);
            $selectURL->execute();
            $selectURLResults = $selectURL->get_result();
            if ($selectURLResults->num_rows == 1) {
                $row = $selectURLResults->fetch_assoc();
                $url = $row['image_path'];
                $fileKey = str_replace("https://vventureinv.s3.us-east-2.amazonaws.com/","",$url);
                $bucketName = 'vventureinv';
                $IAM_KEY = 'AKIAJ6VDWA3J2OM5L7WA';
                $IAM_SECRET = 'DMW8iNueUzOmsF/00DmAb9ImuxpYsWh7dKeonDdn';

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

                $updateStatement = $conn->prepare("DELETE FROM `images_investor` WHERE id_investor=? AND id_image=?");
                $updateStatement->bind_param("ii", $id, $id_image);
                $updateStatement->execute();

                $myObj->res = "success";
                $JSON = json_encode($myObj);
                echo $JSON;
            } else {
                $myObj->res = "image does not exist";
                $JSON = json_encode($myObj);
                echo $JSON;
            }
        } else {
            $myObj->res = "user error";
            $JSON = json_encode($myObj);
            echo $JSON;
        }
    } else {
        $myObj->res = "no post info";
        $JSON = json_encode($myObj);
        echo $JSON;
    }
} else {
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}
