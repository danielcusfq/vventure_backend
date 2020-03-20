<?php
require_once ('../../../../vendor/autoload.php');
require_once ('../../../../validation/Validation.php');

$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && !empty($_GET{'id'}) && !empty(isset($_GET['token'])) && $_GET['auth'] == "93d558beec793039706264467f6e15463c34c552f968efd6ce09db62e2d489cb") {
    require_once("../../../../connection.php");
    $id = $_GET{'id'};
    $token = $_GET{'token'};
    $type = 1;

    if (Validation::VerifyUser($id, $type, $token, $conn) == true) {
        $getProfile = $conn->prepare("SELECT user_entrepreneur.id, user_entrepreneur.organization, user_entrepreneur.name, user_entrepreneur.last_name, 
                    profile_entrepreneur.profile_picture, profile_entrepreneur.profile_video, profile_entrepreneur.stage, 
                    profile_entrepreneur.stake, profile_entrepreneur.stake_info, profile_entrepreneur.solution, profile_entrepreneur.problem FROM user_entrepreneur JOIN profile_entrepreneur WHERE 
                    user_entrepreneur.id=? AND user_entrepreneur.token=? AND user_entrepreneur.id=profile_entrepreneur.id_entrepreneur");
        $getProfile->bind_param("is", $id, $token);
        $getProfile->execute();
        $getProfileResults = $getProfile->get_result();

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

        $html =     "<div style='width: 800px' align='center'>
                <h1 align='center'>My Profile</h1>
                <div style='width: 100%; padding-top: 25px' align='center'>
                    <img src='$image' width='120' height='120'>
                </div>
                <div align='center'>
                    <div align='center'>
                        <div align='center' style='font-weight: bold'>Organization</div>
                        $organization
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>Run By</div>
                        $name $last
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>Stage</div>
                        $stage
                        <br>
                        <br>
                        You are giving $stake
                        <br>
                        In exchange of $stakeInfo
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>You are solving the next problem</div>
                        $problem
                        <br>
                        <br>
                        <div align='center' style='font-weight: bold'>This is how you are solving the problem</div>
                        $solution
                    </div>
                </div>
                <div class='highlights' style='padding-top: 25px; width: 100%' align='center'>
                    <div align='center' style='font-weight: bold'>
                        Highlights
                    </div>
                    $highlights
                </div>
                <div class='info' style='padding-top: 25px; width: 100%' align='center'>
                    <div align='center' style='font-weight: bold'>
                        Info
                    </div>
                    $info
                </div>
                <div class='timeline' style='padding-top: 25px; width: 100%' align='center'>
                    <div align='center' style='font-weight: bold'>
                        Timeline
                    </div>
                    $timeline
                </div>
            </div>
        ";

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/var/www/html/temp', 'mode' => 'utf-8']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('profile.pdf',"D");
    }
}
