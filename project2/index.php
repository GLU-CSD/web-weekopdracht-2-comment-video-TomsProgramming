<?php
require 'config.php';
require 'vendor/autoload.php';

use Carbon\Carbon;

$userInfo = [];

$selectVideosQuery = $con->prepare("SELECT v.id AS video_id, v.thumbnail_path, v.title, v.date_added, u.id AS user_id, u.username, u.profilePicturePath FROM videos v JOIN users u ON v.user_id = u.id ORDER BY v.id DESC LIMIT 20");
$selectVideosQuery->execute();
$result = $selectVideosQuery->get_result();
$videos = $result->fetch_all(MYSQLI_ASSOC);
$selectVideosQuery->close();

if(isset($_SESSION['username'])){
    $getUserInfoQuery = $con->prepare("SELECT username, profilePicturePath FROM users WHERE username = ?");
    $getUserInfoQuery->bind_param("s", $_SESSION['username']);
    $getUserInfoQuery->execute();
    $result = $getUserInfoQuery->get_result();
    $userInfo = $result->fetch_assoc();
    $getUserInfoQuery->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/index.css">
    <title>Youtube</title>
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

    <!-- Side Bar -->

     <div class="sidebar">
         <div class="shortcut-links">
             <a href="#"><img src="./images/home.png"><p>Home</p></a>
             <a href="#"><img src="./images/explore.png"><p>explore</p></a>
             <a href="#"><img src="./images/subscriprion.png"><p>subscriprion</p></a>
             <hr>
             <a href="#"><img src="./images/library.png"><p>library</p></a>
             <a href="#"><img src="./images/history.png"><p>History</p></a>
             <a href="#"><img src="./images/playlist.png"><p>playlist</p></a>
             <a href="#"><img src="./images/messages.png"><p>messages</p></a>
             <a href="#"><img src="./images/show-more.png"><p>Show more</p></a>
             <hr>
            </div>
          <div class="subscribed-list">
              <h3>SUBSCRIPTIONS</h3>
            </div>
        </div>

<!-- Main Body -->
<div class="container">
    <div class="list-container">
        <?php
        foreach($videos as $video){
            ?>
            <div class="vid-list">
                <a href="video.php?v=<?php echo $video['video_id'] ?>"> <img src="<?php echo $video['thumbnail_path'] ?>" class="thumbnail" ></a>
                <div class="flex-div">
                    <img src="<?php echo $video['profilePicturePath'] ?>">
                    <div class="vid-info">
                        <a href="video.php?v=<?php echo $video['video_id'] ?>"><?php echo $video['title'] ?></a>
                        <p><?php echo $video['username'] ?></p>
                        <p>2k Views &bull; <?php echo Carbon::parse($video['date_added'])->locale('nl-NL')->diffForHumans() ?></p>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
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


<script src="assets/js/index.js"></script>

</body>
</html>