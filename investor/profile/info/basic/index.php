<?php

$myObj = (object)array();

if (isset($_GET{'id'}) && isset($_GET['token']) && !empty($_GET{'id'}) && !empty(isset($_GET['token'])) && $_GET['auth'] == "dbfc41327aa4e3658bc31596579209cadf6566cffcb754645b818bc88ba4ec19"){
    require_once("../../../../connection.php");
    //gets data
    $id = $_GET{'id'};
    $token = $_GET{'token'};

    //prepares query
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
        $myObj->res = "success";
        $myObj->id = $row['id'];
        $myObj->organization = $row['organization'];
        $myObj->name = $row['name'];
        $myObj->last = $row['last_name'];
        $myObj->image = $row['profile_picture'];
        $myObj->video = $row['profile_video'];
        $myObj->interests = $row['interests'];
        $myObj->background = $row['background'];
    }

    $userHighlights = array();
    $getHighlight = $conn->prepare("SELECT highlights_investor.id_highlights, highlights_investor.description FROM  
                highlights_investor JOIN user_investor WHERE highlights_investor.id_investor=user_investor.id AND user_investor.id=?");
    $getHighlight->bind_param("i", $id);
    $getHighlight->execute();
    $getHighlightResults = $getHighlight->get_result();
    if ($getHighlightResults->num_rows > 0) {
        while($row = $getHighlightResults->fetch_assoc()){
            $userHighlight = (Object) array();

            $userHighlight->id = $row['id_highlights'];
            $userHighlight->iduser = $id;
            $userHighlight->description = $row['description'];
            array_push($userHighlights, $userHighlight);
        }
    }

    $myObj->highlights = $userHighlights;

    $userInfo = array();
    $getInfo = $conn->prepare("SELECT info_investor.id_info, info_investor.title, info_investor.detail, info_investor.position 
                FROM info_investor JOIN user_investor WHERE info_investor.id_investor=user_investor.id AND user_investor.id=?
                ORDER BY info_investor.position");
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
    $getImages = $conn->prepare("SELECT images_investor.id_image, images_investor.image_path FROM images_investor JOIN 
                    user_investor WHERE images_investor.id_investor=user_investor.id AND user_investor.id=?");
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
    $getTimeline = $conn->prepare("SELECT timeline_investor.id_timeline, timeline_investor.description, timeline_investor.position 
                    FROM timeline_investor JOIN user_investor WHERE timeline_investor.id_investor=user_investor.id AND user_investor.id=?");
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

    //sends response
    $myObj->timeline = $userTimeline;
    $JSON = json_encode($myObj);
    echo $JSON;
} else {
    $myObj->res = "error no auth";
    $JSON = json_encode($myObj);
    echo $JSON;
}
