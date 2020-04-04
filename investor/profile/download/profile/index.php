<?php
require_once ('../../../../vendor/autoload.php');
require_once ('../../../../validation/Validation.php');

$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && !empty($_GET{'id'}) && !empty(isset($_GET['token'])) && $_GET['auth'] == "b4168ab5b11fdb0808e51ce69279566e56a63800a8430aa4555177a17fc8178b") {
    require_once("../../../../connection.php");
    //gets data
    $id = $_GET{'id'};
    $token = $_GET{'token'};
    $type = 2;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //prepare query
        $getProfile = $conn->prepare("SELECT user_investor.id, user_investor.organization, user_investor.name, user_investor.last_name, 
                    profile_investor.profile_picture, profile_investor.profile_video,
                    profile_investor.interests, profile_investor.background FROM user_investor JOIN profile_investor WHERE 
                    user_investor.id=? AND user_investor.token=? AND user_investor.id=profile_investor.id_investor");
        $getProfile->bind_param("is", $id, $token);
        $getProfile->execute();
        $getProfileResults = $getProfile->get_result();
        //fetch information
        if ($getProfileResults->num_rows == 1) {
            $row = $getProfileResults->fetch_assoc();
            $organization = $row['organization'];
            $name = $row['name'];
            $last = $row['last_name'];
            $image = $row['profile_picture'];
            $interests = $row['interests'];
            $background = $row['background'];
        }

        $highlights = "";
        $getHighlight = $conn->prepare("SELECT highlights_investor.id_highlights, highlights_investor.description FROM  
                highlights_investor JOIN user_investor WHERE highlights_investor.id_investor=user_investor.id AND user_investor.id=?");
        $getHighlight->bind_param("i", $id);
        $getHighlight->execute();
        $getHighlightResults = $getHighlight->get_result();
        if ($getHighlightResults->num_rows > 0) {
            while($row = $getHighlightResults->fetch_assoc()){
                $highlights .= "<div class='highlights' align='center'> ".$row['description']." </div> <br>";
            }
        }

        $info = "";
        $getInfo = $conn->prepare("SELECT info_investor.id_info, info_investor.title, info_investor.detail, info_investor.position 
                FROM info_investor JOIN user_investor WHERE info_investor.id_investor=user_investor.id AND user_investor.id=?
                ORDER BY info_investor.position");
        $getInfo->bind_param("i", $id);
        $getInfo->execute();
        $getInfoResults = $getInfo->get_result();
        if ($getInfoResults->num_rows > 0) {
            while ($row = $getInfoResults->fetch_assoc()) {
                $info .= "<div class='info' align='center'>
                    <div class='title' align='center'>
                        ".$row['title']."
                    </div>
                    <div class='description' align='center'>
                        ".$row['detail']."
                    </div>
                </div>
                <br>";
            }
        }

        $timeline = "";
        $getTimeline = $conn->prepare("SELECT timeline_investor.id_timeline, timeline_investor.description, timeline_investor.position 
                    FROM timeline_investor JOIN user_investor WHERE timeline_investor.id_investor=user_investor.id AND user_investor.id=?");
        $getTimeline->bind_param("i", $id);
        $getTimeline->execute();
        $getTimelineResults = $getTimeline->get_result();
        if ($getTimelineResults->num_rows > 0) {
            while ($row = $getTimelineResults->fetch_assoc()) {
                $timeline .= "<div class='timeline' align='center'>".$row['description']."</div> <br>";
            }
        }

        $html =     "<div style='width: 800px' align='center'>
                <h1 align='center'>Mi Perfil</h1>
                <div style='width: 100%; padding-top: 25px' align='center'>
                    <img src='$image' width='120' height='120'>
                </div>
                <div align='center'>
                    <div align='center'>
                        <div align='center' style='font-weight: bold'>Organización</div>
                        $organization
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>Representado Por</div>
                        $name $last
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>Estos son tus Intereses</div>
                        $interests
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>Estos son tus Antecedentes</div>
                        $background
                    </div>
                </div>
                <div class='highlights' style='padding-top: 25px; width: 100%' align='center'>
                    <div align='center' style='font-weight: bold'>
                        Aspectos Destacados
                    </div>
                    $highlights
                </div>
                <div class='info' style='padding-top: 25px; width: 100%' align='center'>
                    <div align='center' style='font-weight: bold'>
                        Información
                    </div>
                    $info
                </div>
                <div class='timeline' style='padding-top: 25px; width: 100%' align='center'>
                    <div align='center' style='font-weight: bold'>
                        Línea de Tiempo
                    </div>
                    $timeline
                </div>
            </div>
        ";

        //creates PDF and sends to downloads
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/var/www/html/temp', 'mode' => 'utf-8']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('profile.pdf','D');
    }

}
