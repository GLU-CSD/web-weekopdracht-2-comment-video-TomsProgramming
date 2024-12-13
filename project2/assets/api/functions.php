<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include("../../config.php");

require '../../vendor/autoload.php';

use YoHang88\LetterAvatar\LetterAvatar;
use Carbon\Carbon;

$raw_post_data = file_get_contents('php://input');
$post_data = json_decode($raw_post_data, true);
if(isset($post_data['function']) || isset($_POST['function'])){
    $function = $post_data['function'] ?? $_POST['function'];

    if($function == 'register' && isset($post_data['username'], $post_data['email'], $post_data['password'], $post_data['confirm_password'])){
        $username = $post_data['username'];
        $email = $post_data['email'];
        $password = $post_data['password'];
        $confirm_password = $post_data['confirm_password'];

        if(isset($_SESSION['username'])){
            echo json_encode(array('status' => 'error', 'message' => 'You are already logged in'));
            exit;
        }

        $checkUsernameQuery = $con->prepare("SELECT id FROM users WHERE username = ?");
        $checkUsernameQuery->bind_param("s", $username);
        $checkUsernameQuery->execute();
        $checkUsernameQuery->store_result();
        
        if ($checkUsernameQuery->num_rows > 0) {
            echo json_encode(array('status' => 'error', 'message' => 'Username already taken'));
            $checkUsernameQuery->close();
            exit;
        }

        $checkEmailQuery = $con->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmailQuery->bind_param("s", $email);
        $checkEmailQuery->execute();
        $checkEmailQuery->store_result();
        
        if ($checkEmailQuery->num_rows > 0) {
            echo json_encode(array('status' => 'error', 'message' => 'Email already taken'));
            $checkEmailQuery->close();
            exit;
        }

        $checkUsernameQuery->close();
        $checkEmailQuery->close();

        if($password == $confirm_password){
            $password = password_hash($password, PASSWORD_ARGON2ID, $encryptionOptions);

            $insertUserQuery = $con->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if($insertUserQuery == false){
                prettyDump( mysqli_error($con) );
            }

            $insertUserQuery->bind_param("sss", $username, $email, $password);
            if($insertUserQuery->execute() == false){
                prettyDump( mysqli_error($con) );
            }else{
                $lastId = $con->insert_id;
                $avatar = new LetterAvatar($username);
                $save = $avatar->saveAs("../../uploads/profile_pictures/$lastId.png");

                $updateQuery = $con->prepare("UPDATE users SET profilePicturePath = 'uploads/profile_pictures/$lastId.png' WHERE id = ?");
                $updateQuery->bind_param("i", $lastId);
                $updateQuery->execute();
                $updateQuery->close();

                $_SESSION['username'] = $username;
                echo json_encode(array('status' => 'success'));
            }
            $insertUserQuery->close();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Passwords do not match'));
        }
    }

    if($function == 'login' && isset($post_data['username'], $post_data['password'])){
        $username = $post_data['username'];
        $password = $post_data['password'];

        if(isset($_SESSION['username'])){
            echo json_encode(array('status' => 'error', 'message' => 'You are already logged in'));
            exit;
        }

        $getUserQuery = $con->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $getUserQuery->bind_param("s", $username);
        $getUserQuery->execute();
        $getUserQuery->store_result();
        
        if ($getUserQuery->num_rows > 0) {
            $getUserQuery->bind_result($id, $username, $hashed_password);
            $getUserQuery->fetch();

            if(password_verify($password, $hashed_password)){
                $_SESSION['username'] = $username;
                echo json_encode(array('status' => 'success'));
            }else{
                echo json_encode(array('status' => 'error', 'message' => 'Invalid password'));
            }
        }else{
            echo json_encode(array('status' => 'error', 'message' => 'User not found'));
        }
        $getUserQuery->close();
    }
    
    if($function == 'uploadVideo' && isset($_FILES['video'], $_POST['title'], $_POST['description'])){
        
        $video = $_FILES['video'];
        $title = $_POST['title'];
        $description = $_POST['description'];

        if(!isset($_SESSION['username'])){
            echo json_encode(array('status' => 'error', 'message' => 'You must be logged in to upload a video'));
            exit;
        }

        $selectUserQuery = $con->prepare("SELECT id FROM users WHERE username = ?");
        $selectUserQuery->bind_param("s", $_SESSION['username']);
        $selectUserQuery->execute();
        $result = $selectUserQuery->get_result();
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'User not found'));
            exit;
        }
        $selectUserQuery->close();

        $userId = $userData['id'];

        $videoName = $video['name'];
        $videoTmpName = $video['tmp_name'];
        $videoSize = $video['size'];
        $videoError = $video['error'];

        $videoExt = explode('.', $videoName);
        $videoActualExt = strtolower(end($videoExt));

        $allowed = array('mp4', 'mov');

        if(in_array($videoActualExt, $allowed)){
            if($videoError === 0){
                if($videoSize < 1000000000){// 1 gb
                    $inserVideoQuery = $con->prepare("INSERT INTO videos (user_id, title, description) VALUES (?, ?, ?)");
                    $inserVideoQuery->bind_param("iss", $userId, $title, $description);
                    $inserVideoQuery->execute();

                    $lastId = $con->insert_id;

                    if(!is_dir('../../uploads/videos/' . $userId)){
                        mkdir('../../uploads/videos/' . $userId, 0777, true);
                    }

                    $videoNameNew = "video".$lastId.".".$videoActualExt;
                    $videoDestination = '../../uploads/videos/'.$userId.'/'.$videoNameNew;
                    move_uploaded_file($videoTmpName, $videoDestination);
                    $videoPath = 'uploads/videos/'.$userId.'/'.$videoNameNew;

                    $thumbnailPath = 'images/default-thumbnail.png';

                    if(isset($_FILES['thumbnail'])){
                        $thumbnail = $_FILES['thumbnail'];
                        $thumbnailTmpName = $thumbnail['tmp_name'];
                        $thumbnailSize = $thumbnail['size'];
                        $thumbnailError = $thumbnail['error'];
                        $thumbnailActualExt = strtolower(end(explode('.', $thumbnail['name'])));

                        $allowedThumbnail = array('jpg', 'jpeg', 'png');

                        if(in_array($thumbnailActualExt, $allowedThumbnail)){
                            if($thumbnailError === 0){
                                if ($thumbnailSize < 10000000) { // 10 MB
                                    $thumbnailNameNew = "thumbnail".$lastId.".".$thumbnailActualExt;
                                    $thumbnailDestination = '../../uploads/thumbnails/'.$thumbnailNameNew;
                                    move_uploaded_file($thumbnailTmpName, $thumbnailDestination);

                                    $thumbnailPath = 'uploads/thumbnails/'.$thumbnailNameNew;
                                }
                            }
                        }
                    }

                    $updateVideoQuery = $con->prepare("UPDATE videos SET video_path = ?, thumbnail_path = ? WHERE id = ?");
                    $updateVideoQuery->bind_param("ssi", $videoPath, $thumbnailPath, $lastId);
                    $updateVideoQuery->execute();
                    $updateVideoQuery->close();

                    echo json_encode(array('status' => 'success', 'videoId' => $lastId));
                }else{
                    echo json_encode(array('status' => 'error', 'message' => 'Your file is too big'));
                }
            }else{
                echo json_encode(array('status' => 'error', 'message' => 'There was an error uploading your file'));
            }
        }else{
            echo json_encode(array('status' => 'error', 'message' => 'You cannot upload files of this type'));
        }
    }

    if($function == 'getComments' && isset($post_data['video_id'])){
        $video_id = $post_data['video_id'];

        $getCommentsQuery = $con->prepare("SELECT c.message, c.date_added, u.username, u.profilePicturePath FROM comments c JOIN users u ON c.user_id = u.id WHERE c.video_id = ? ORDER BY c.id DESC LIMIT 20");
        $getCommentsQuery->bind_param("i", $video_id);
        $getCommentsQuery->execute();
        $result = $getCommentsQuery->get_result();
        $comments = $result->fetch_all(MYSQLI_ASSOC);
        $getCommentsQuery->close();

        foreach($comments as $key => $comment){
            $comments[$key]['time_ago'] = Carbon::parse($comment['date_added'])->locale('nl-NL')->diffForHumans();
        }
        
        echo json_encode(array('status' => 'success', 'comments' => $comments));
    }

    if($function == 'addComment' && isset($post_data['video_id'], $post_data['message'])){
        $video_id = $post_data['video_id'];
        $message = $post_data['message'];

        if(!isset($_SESSION['username'])){
            echo json_encode(array('status' => 'error', 'message' => 'You must be logged in to comment'));
            exit;
        }

        $selectUserQuery = $con->prepare("SELECT id FROM users WHERE username = ?");
        $selectUserQuery->bind_param("s", $_SESSION['username']);
        $selectUserQuery->execute();
        $result = $selectUserQuery->get_result();
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'User not found'));
            exit;
        }
        $selectUserQuery->close();

        $user_id = $userData['id'];

        $addCommentQuery = $con->prepare("INSERT INTO comments (video_id, user_id, message) VALUES (?, ?, ?)");
        $addCommentQuery->bind_param("iis", $video_id, $user_id, $message);
        $addCommentQuery->execute();
        $addCommentQuery->close();

        echo json_encode(array('status' => 'success'));
    }

    if($function == 'updateVideoHistory' && isset($post_data['video_id'], $post_data['current_time'])){
        $video_id = $post_data['video_id'];
        $current_time = floor($post_data['current_time']);

        if(!isset($_SESSION['username'])){
            echo json_encode(array('status' => 'error', 'message' => 'You must be logged in to update video history'));
            exit;
        }

        $selectUserQuery = $con->prepare("SELECT id FROM users WHERE username = ?");
        $selectUserQuery->bind_param("s", $_SESSION['username']);
        $selectUserQuery->execute();
        $result = $selectUserQuery->get_result();
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'User not found'));
            exit;
        }

        $user_id = $userData['id'];
        
        $checkVideoHistoryQuery = $con->prepare("SELECT id, video_id FROM video_history WHERE user_id = ? ORDER BY last_viewed DESC LIMIT 1");
        $checkVideoHistoryQuery->bind_param("i", $user_id);
        $checkVideoHistoryQuery->execute();
        $checkVideoHistoryResult = $checkVideoHistoryQuery->get_result();
        $checkVideoHistoryQuery->close();

        if ($checkVideoHistoryResult->num_rows > 0) {
            $checkVideoHistoryRow = $checkVideoHistoryResult->fetch_assoc();
            $last_video_id = $checkVideoHistoryRow['video_id'];

            if ($last_video_id == $video_id) {
                $updateHistoryQuery = $con->prepare("UPDATE video_history SET last_position = ?, last_viewed = ? WHERE user_id = ? AND video_id = ?");
                $updateHistoryQuery->bind_param("isii", $current_time, date("Y-m-d H:i:s"), $user_id, $video_id);
                $updateHistoryQuery->execute();
                $updateHistoryQuery->close();

                echo json_encode(array('status' => 'success', 'message' => 'Video history updated'));
            } else {
                $insertHistoryQuery = $con->prepare("INSERT INTO video_history (user_id, video_id, last_position) VALUES (?, ?, ?)");
                $insertHistoryQuery->bind_param("iii", $user_id, $video_id, $current_time);
                $insertHistoryQuery->execute();
                $insertHistoryQuery->close();

                echo json_encode(array('status' => 'success', 'message' => 'Video history added'));     
            }
        }else{
            $insertHistoryQuery = $con->prepare("INSERT INTO video_history (user_id, video_id, last_position) VALUES (?, ?, ?)");
            $insertHistoryQuery->bind_param("iii", $user_id, $video_id, $current_time);
            $insertHistoryQuery->execute();
            $insertHistoryQuery->close();
            
            echo json_encode(array('status' => 'success', 'message' => 'Video history added'));
        }
    }

    if($function == 'getVideoHistory' && isset($post_data['video_id'])){
        $video_id = $post_data['video_id'];

        if(!isset($_SESSION['username'])){
            echo json_encode(array('status' => 'error', 'message' => 'You must be logged in to get video history'));
            exit;
        }

        $selectUserQuery = $con->prepare("SELECT id FROM users WHERE username = ?");
        $selectUserQuery->bind_param("s", $_SESSION['username']);
        $selectUserQuery->execute();
        $result = $selectUserQuery->get_result();
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'User not found'));
            exit;
        }

        $user_id = $userData['id'];

        $getVideoHistoryQuery = $con->prepare("SELECT last_position FROM video_history WHERE user_id = ? AND video_id = ? LIMIT 1");
        $getVideoHistoryQuery->bind_param("ii", $user_id, $video_id);
        $getVideoHistoryQuery->execute();
        $result = $getVideoHistoryQuery->get_result();
        if ($result->num_rows > 0) {
            $videoHistory = $result->fetch_assoc();
            echo json_encode(array('status' => 'success', 'last_position' => $videoHistory['last_position']));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'No video history found'));
        }
    }
}
?>