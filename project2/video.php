<?php
if(!isset($_GET['v'])){
    echo '<script>window.location.href = "./";</script>';
    exit;
}

require 'config.php';
require 'vendor/autoload.php';

use Carbon\Carbon;

$userInfo = [];

if(isset($_SESSION['username'])){
    $getUserInfoQuery = $con->prepare("SELECT username, profilePicturePath FROM users WHERE username = ?");
    $getUserInfoQuery->bind_param("s", $_SESSION['username']);
    $getUserInfoQuery->execute();
    $result = $getUserInfoQuery->get_result();
    $userInfo = $result->fetch_assoc();
    $getUserInfoQuery->close();
}

$videoId = $_GET['v'];

$selectVideoQuery = $con->prepare("SELECT * FROM videos WHERE id = ?");
$selectVideoQuery->bind_param("i", $videoId);
$selectVideoQuery->execute();
$result = $selectVideoQuery->get_result();
$video = $result->fetch_assoc();

$videoOwnerSelectQuery = $con->prepare("SELECT username, profilePicturePath FROM users WHERE id = ?");
$videoOwnerSelectQuery->bind_param("i", $video['user_id']);
$videoOwnerSelectQuery->execute();
$result = $videoOwnerSelectQuery->get_result();
$videoOwner = $result->fetch_assoc();

$selectVideosQuery = $con->prepare("SELECT v.id AS video_id, v.thumbnail_path, v.title, v.date_added, u.id AS user_id, u.username, u.profilePicturePath FROM videos v JOIN users u ON v.user_id = u.id ORDER BY v.id DESC LIMIT 20");
$selectVideosQuery->execute();
$result = $selectVideosQuery->get_result();
$videos = $result->fetch_all(MYSQLI_ASSOC);
$selectVideosQuery->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/index.css">
    <title><?php echo $video['title'] ?></title>
</head>

<body>
    <nav class="flex-div">
        <div class="nav-left flex-div">
            <img src="./images/menu.png" class="menu-icon" alt="" srcset="">
            <img src="./images/logo.png" class="logo" alt="" srcset="">
        </div>
        <div class="nav-middle flex-div">
            <div class="search-box flex-div">
                <input type="text" placeholder="Search..">
                <img src="./images/search.png" alt="" srcset="">
            </div>
        </div>
        <div class="nav-right flex-div">
            <img class="upload-video" src="./images/upload.png" alt="" srcset="">
            <img src="./images/more.png" alt="" srcset="">
            <img src="./images/notification.png" alt="" srcset="">
            <?php
            if(isset($userInfo['profilePicturePath'])){
                echo '<img src="'.$userInfo['profilePicturePath'].'" class="user-icon" alt="" srcset="">';
            }else{
                echo '<button onclick="login()" class="login-btn">Login</button>';
            }
            ?>
        </div>
    </nav>


    <div class="container play-container">
        <div class="row">
            <div class="play-video">
                <video class="video" controls autoplay>
                    <source src="<?php echo $video['video_path'] ?>" type="video/mp4">
                </video>
                <h3><?php echo $video['title'] ?></h3>
                <div class="play-video-info">
                    <p>18,406,599 Views &bull; <?php echo Carbon::parse($video['date_added'])->locale('nl-NL')->diffForHumans() ?></p>
                    <div>
                        <a href="http://"><img src="./images/like.png">488K</a>
                        <a href="http://"><img src="./images/dislike.png">5.9K</a>
                        <a href="http://"><img src="./images/share.png">SHARE</a>
                        <a href="http://"><img src="./images/save.png">SAVE</a>
                    </div>
                </div>
                <hr>
                <div class="owner">
                    <img src="<?php echo $videoOwner['profilePicturePath'] ?>">
                    <div>
                        <p><?php echo $videoOwner['username'] ?></p>
                        <span>19.4K subscribers</span>
                    </div>
                    <button type="button">Subscribe</button>

                </div>

                <div class="vid-des">
                    <p><?php echo $video['description'] ?></p>
                    <hr>
                    <div class="cmnt">
                        <h4>16,303 Commnets</h4>
                    </div>

                    <?php
                    if(isset($userInfo['profilePicturePath'])){
                    ?>
                        <form class="add-cmnt">
                            <img src="<?php echo $userInfo['profilePicturePath'] ?>" alt="User profile">
                            <input type="text" name="comment" id="message" placeholder="Plaats een comment" required>
                            <button type="submit" hidden></button>
                        </form>
                    <?php
                    }else{
                    ?>
                        <div class="add-cmnt">
                            <button onclick="login()" class="login-btn">Login</button>
                        </div>
                    <?php
                    }
                    ?>

                    <div class="cmnts" data-video-id="<?php echo $videoId ?>">
                    </div>
                </div>
                <hr class="hide-hr">


            </div>
            <div class="right-sidebar">
                <?php
                foreach($videos as $video){
                    echo '<div class="side-video-list">
                        <a href="video.php?v='.$video['video_id'].'" class="small-thumbnail"> <img src="'.$video['thumbnail_path'].'" alt="" srcset=""></a>
                        <div class="vid-info">
                            <a href="video.php?v='.$video['video_id'].'">'.$video['title'].'</a>
                            <p>'.$video['username'].'</p>
                            <p>1.1M Views</p>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>

        <div class="upload">
            <div class="upload-box">
                <h2>Upload Your Video</h2>
                <form class="uploadForm">
                    <div class="form-group">
                        <label for="video">Video File:</label>
                        <input type="file" id="video" accept="video/*" required>
                    </div>

                    <div class="form-group">
                        <label for="title">Video Title:</label>
                        <input type="text" id="title" placeholder="Enter video title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Video Description:</label>
                        <textarea id="description" placeholder="Enter video description" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="thumbnail">Thumbnail Image:</label>
                        <input type="file" id="thumbnail" accept="image/*">
                    </div>

                    <button type="submit">Upload</button>
                </form>

                <div class="back">
                    <a>Back</a>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="assets/js/index.js"></script>
</html>