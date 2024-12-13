<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../config.php");
include("../../reactions.php");

header('Content-Type: application/json');

$raw_post_data = file_get_contents('php://input');
$post_data = json_decode($raw_post_data, true);
if(isset($post_data['function'])){
    $function = $post_data['function'];

    if($function == 'getComments' && isset($post_data['videoId'])){
        $videoId = $post_data['videoId'];
        $comments = Reactions::getReactions($videoId);
        echo json_encode($comments);
    }

    if($function == 'setComment' && isset($post_data['video_id']) && isset($post_data['name']) && isset($post_data['email']) && isset($post_data['message'])){
        $comment = Reactions::setReaction($post_data);
        if(isset($comment['error'])){
            echo json_encode($comment);
        }else{
            echo json_encode($comment);
        }
    }
}
?>