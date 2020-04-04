<?php
require_once ('../../../../vendor/autoload.php');
require_once ('../../../../validation/Validation.php');

$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && !empty($_GET{'id'}) && !empty(isset($_GET['token'])) && $_GET['auth'] == "93d558beec793039706264467f6e15463c34c552f968efd6ce09db62e2d489cb") {
    require_once("../../../../connection.php");
    //gets data
    $id = $_GET{'id'};
    $token = $_GET{'token'};
    $type = 1;

    //validates user
    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        //prepare query
        $getProfile = $conn->prepare("SELECT user_entrepreneur.id, user_entrepreneur.organization, user_entrepreneur.name, user_entrepreneur.last_name, 
                    profile_entrepreneur.profile_picture, profile_entrepreneur.profile_video, profile_entrepreneur.stage, 
                    profile_entrepreneur.stake, profile_entrepreneur.stake_info, profile_entrepreneur.solution, profile_entrepreneur.problem FROM user_entrepreneur JOIN profile_entrepreneur WHERE 
                    user_entrepreneur.id=? AND user_entrepreneur.token=? AND user_entrepreneur.id=profile_entrepreneur.id_entrepreneur");
        $getProfile->bind_param("is", $id, $token);
        $getProfile->execute();
        $getProfileResults = $getProfile->get_result();

        //fetch user data
        if ($getProfileResults->num_rows == 1) {
            $row = $getProfileResults->fetch_assoc();
            $organization = $row['organization'];
            $name = $row['name'];
            $last = $row['last_name'];
            $image = $row['profile_picture'];
            $stage = $row['stage'];
            $stake = $row['stake'];
            $stakeInfo = $row['stake_info'];
            $problem = $row['problem'];
            $solution = $row['solution'];
        }

        $highlights = "";
        //fetch user data
        $getHighlight = $conn->prepare("SELECT highlights_entrepreneur.id_highlight, highlights_entrepreneur.description FROM  
                highlights_entrepreneur JOIN user_entrepreneur WHERE highlights_entrepreneur.id_entrepreneur=user_entrepreneur.id AND user_entrepreneur.id=?");
        $getHighlight->bind_param("i", $id);
        $getHighlight->execute();
        $getHighlightResults = $getHighlight->get_result();
        if ($getHighlightResults->num_rows > 0) {
            while($row = $getHighlightResults->fetch_assoc()){
                $highlights .= "<div class='highlights' align='center'> ".$row['description']." </div> <br>";
            }
        }
        //fetch user data
        $info = "";
        $getInfo = $conn->prepare("SELECT info_entrepreneur.id_info, info_entrepreneur.title, info_entrepreneur.detail, info_entrepreneur.position 
                FROM info_entrepreneur JOIN user_entrepreneur WHERE info_entrepreneur.id_entrepreneur=user_entrepreneur.id AND user_entrepreneur.id=?
                ORDER BY info_entrepreneur.position");
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

        //fetch user data
        $timeline = "";
        $getTimeline = $conn->prepare("SELECT timeline_entrepreneur.id_timeline, timeline_entrepreneur.description, timeline_entrepreneur.position 
                    FROM timeline_entrepreneur JOIN user_entrepreneur WHERE timeline_entrepreneur.id_entrepreneur=user_entrepreneur.id AND user_entrepreneur.id=?");
        $getTimeline->bind_param("i", $id);
        $getTimeline->execute();
        $getTimelineResults = $getTimeline->get_result();
        if ($getTimelineResults->num_rows > 0) {
            while ($row = $getTimelineResults->fetch_assoc()) {
                $timeline .= "<div class='timeline' align='center'>".$row['description']."</div> <br>";
            }
        }

        //creates html to get converted to PDF
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
                        <div align='center' style='font-weight: bold'>Etapa</div>
                        $stage
                        <br>
                        <br>
                        Estas Dando el $stake %
                        <br>
                        De tu Compañía a cambio de $stakeInfo
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>Estás Resolviendo el Siguiente Problema</div>
                        $problem
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>Así es como Estás Resolviendo el Problema</div>
                        $solution
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

        //converts to PDF and send PDF to download
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/var/www/html/temp', 'mode' => 'utf-8']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('profile.pdf',"D");
    }
}
