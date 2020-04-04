<?php
require('../../../../validation/Validation.php');
require ("../../../../aws/aws-autoloader.php");
$myObj = (object)array();

if (isset($_POST{'id'}) && isset($_POST['token']) && isset($_POST['type']) && !empty($_POST{'id'}) && !empty(isset($_POST['token'])) && $_POST['auth'] == 'eb432260e66deef8a6482ae9cebf98f5faabbcc0f19ce08b5edeb1bbdd043457' && $_POST['type'] == 1) {
    if (isset($_POST['id_image']) && !empty($_POST['id_image'])) {
        require("../../../../connection.php");
        //gets data
        $id = $_POST['id'];
        $type = $_POST['type'];
        $token = $_POST['token'];
        $id_image = $_POST['id_image'];

        //validates user
        if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
            //prepares query
            $selectURL = $conn->prepare("SELECT `image_path` FROM images_entrepreneur WHERE id_entrepreneur=? AND id_image=?");
            $selectURL->bind_param("ii", $id, $id_image);
            $selectURL->execute();
            $selectURLResults = $selectURL->get_result(); //gets url
            if ($selectURLResults->num_rows == 1) {
                $row = $selectURLResults->fetch_assoc();
                $url = $row['image_path'];
                $fileKey = str_replace("https://vventureent.s3.us-east-2.amazonaws.com/","",$url);
                $bucketName = 'vventureent';
                $IAM_KEY = 'AKIAJ6VDWA3J2OM5L7WA';
                $IAM_SECRET = 'DMW8iNueUzOmsF/00DmAb9ImuxpYsWh7dKeonDdn';

                //create S3 instance
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

                //deletes image
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

                //updates db
                $updateStatement = $conn->prepare("DELETE FROM `images_entrepreneur` WHERE id_entrepreneur=? AND id_image=?");
                $updateStatement->bind_param("ii", $id, $id_image);
                $updateStatement->execute();

                //sends response
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
