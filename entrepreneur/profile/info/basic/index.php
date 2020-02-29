<?php

$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && !empty($_GET{'id'}) && !empty(isset($_GET['token'])) && $_GET['auth'] == "ee046aa3e8cba86d08f5c902c2dba507c7ec6c63da3cbc0262ff2e5d3f969854"){
    require_once("../../../../connection.php");
    $id = $_GET{'id'};
    $token = $_GET{'token'};

    $getProfile = $conn->prepare("SELECT user_entrepreneur.id, user_entrepreneur.organization, user_entrepreneur.name, user_entrepreneur.last_name, 
                    profile_entrepreneur.profile_picture, profile_entrepreneur.profile_video, profile_entrepreneur.stage, 
                    profile_entrepreneur.stake, profile_entrepreneur.stake_info, profile_entrepreneur.solution, profile_entrepreneur.problem FROM user_entrepreneur JOIN profile_entrepreneur WHERE 
                    user_entrepreneur.id=? AND user_entrepreneur.token=? AND user_entrepreneur.id=profile_entrepreneur.id_entrepreneur");
    $getProfile->bind_param("is", $id, $token);
    $getProfile->execute();
    $getProfileResults = $getProfile->get_result();

    if ($getProfileResults->num_rows == 1) {
        $row = $getProfileResults->fetch_assoc();
        $myObj->res = "success";
        $myObj->id = $row['id'];
        $myObj->organization = $row['organization'];
        $myObj->name = $row['name'];
        $myObj->last = $row['last_name'];
        $myObj->image = $row['profile_picture'];
        $myObj->video = $row['profile_video'];
        $myObj->stage = $row['stage'];
        $myObj->stake = $row['stake'];
        $myObj->stakeinfo = $row['stake_info'];
        $myObj->problem = $row['problem'];
        $myObj->solution = $row['solution'];
    }

    $userHighlights = array();
    $getHighlight = $conn->prepare("SELECT highlights_entrepreneur.id_highlight, highlights_entrepreneur.description FROM  
                highlights_entrepreneur JOIN user_entrepreneur WHERE highlights_entrepreneur.id_entrepreneur=user_entrepreneur.id AND user_entrepreneur.id=?");
    $getHighlight->bind_param("i", $id);
    $getHighlight->execute();
    $getHighlightResults = $getHighlight->get_result();
    if ($getHighlightResults->num_rows > 0) {
        while($row = $getHighlightResults->fetch_assoc()){
            $userHighlight = (Object) array();

            $userHighlight->id = $row['id_highlight'];
            $userHighlight->iduser = $id;
            $userHighlight->description = $row['description'];
            array_push($userHighlights, $userHighlight);
        }
    }

    $myObj->highlights = $userHighlights;

    $userInfo = array();
    $getInfo = $conn->prepare("SELECT info_entrepreneur.id_info, info_entrepreneur.title, info_entrepreneur.detail, info_entrepreneur.position 
                FROM info_entrepreneur JOIN user_entrepreneur WHERE info_entrepreneur.id_entrepreneur=user_entrepreneur.id AND user_entrepreneur.id=?
                ORDER BY info_entrepreneur.position");
    $getInfo->bind_param("i", $id);
    $getInfo->execute();
    $getInfoResults = $getInfo->get_result();
    if ($getInfoResults->num_rows > 0) {
        while ($row = $getInfoResults->fetch_assoc()) {
            $info = (object) array();

            $info->id = $row['id_info'];
            $info->idperson = $id;
            $info->title = $row['title'];
            $info->content = $row['detail'];
            $info->pos = $row['position'];
            array_push($userInfo, $info);
        }
    }

    $myObj->info = $userInfo;

    $userImages = array();
    $getImages = $conn->prepare("SELECT images_entrepreneur.id_image, images_entrepreneur.image_path FROM images_entrepreneur JOIN 
                    user_entrepreneur WHERE images_entrepreneur.id_entrepreneur=user_entrepreneur.id AND user_entrepreneur.id=?");
    $getImages->bind_param("i", $id);
    $getImages->execute();
    $getImagesResults = $getImages->get_result();
    if ($getInfoResults->num_rows > 0) {
        while ($row = $getImagesResults->fetch_assoc()) {
            $image = (object) array();

            $image->id = $row['id_image'];
            $image->iduser = $id;
            $image->image = $row['image_path'];
            array_push($userImages, $image);
        }
    }

    $myObj->images = $userImages;

    $userTimeline = array();
    $getTimeline = $conn->prepare("SELECT timeline_entrepreneur.id_timeline, timeline_entrepreneur.description, timeline_entrepreneur.position 
                    FROM timeline_entrepreneur JOIN user_entrepreneur WHERE timeline_entrepreneur.id_entrepreneur=user_entrepreneur.id AND user_entrepreneur.id=?");
    $getTimeline->bind_param("i", $id);
    $getTimeline->execute();
    $getTimelineResults = $getTimeline->get_result();
    if ($getTimelineResults->num_rows > 0) {
        while ($row = $getTimelineResults->fetch_assoc()) {
            $tl = (object) array();

            $tl->id = $row['id_timeline'];
            $tl->iduser = $id;
            $tl->detail = $row['description'];
            $tl->position = $row['position'];
            array_push($userTimeline, $tl);
        }
    }

    $myObj->timeline = $userTimeline;
    $JSON = json_encode($myObj);
    echo $JSON;
} else {
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}
