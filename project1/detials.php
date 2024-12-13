<?php
include("config.php");
include("reactions.php");

require 'vendor/autoload.php';

use Carbon\Carbon;

$client = new \GuzzleHttp\Client();

if(isset($_GET['id'])){
    $movieid = $_GET['id'];

    $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/'.$movieid.'/videos?language=en-US', [
        'headers' => [
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJmYTMyM2I4MWIwMmI3NTk0YmZjNTcxYjdmYzQyMGI4NyIsIm5iZiI6MTczMzkwOTQzMy43MzYsInN1YiI6IjY3NTk1YmI5YWQ5MTg3MjFkMGRlZTQ1ZSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.f7vsQIqKz5TaH_Rc2iFS-KMZWHJ5ScOWKzl25o6Eocg',
        'accept' => 'application/json',
        ],
    ]);

    $result = json_decode($response->getBody(), true)['results'];

    $movieInfo = [];

    $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/'.$movieid, [
        'headers' => [
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJmYTMyM2I4MWIwMmI3NTk0YmZjNTcxYjdmYzQyMGI4NyIsIm5iZiI6MTczMzkwOTQzMy43MzYsInN1YiI6IjY3NTk1YmI5YWQ5MTg3MjFkMGRlZTQ1ZSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.f7vsQIqKz5TaH_Rc2iFS-KMZWHJ5ScOWKzl25o6Eocg',
        'accept' => 'application/json',
        ],
    ]);

    $movieInfo = json_decode($response->getBody(), true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Bekijken</title>
    <link rel="stylesheet" href="assets/css/detials.css">
</head>
<body>
    <div class="container">
        <?php
        if(isset($result) && isset($result[0])){
            $video_key = $result[0]['key'];
            $video_name = $result[0]['name'];

            echo "<h1>Bekijken: {$movieInfo['title']}</h1>";
            echo "<div class='video-container'>";
            echo "<iframe src='https://www.youtube.com/embed/{$video_key}?autoplay=1' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
            echo "</div>";
            echo "<a href='index.php' class='back-button'>Terug naar de film lijst</a>";




            ?>

            <div class="comments-section">
                <h2>Reacties</h2>
                <form class="addCommentForm" method="POST" action="">
                    <input type="text" name="name" placeholder="Je naam" required>
                    <input type="email" name="email" placeholder="Je e-mailadres" required>
                    <textarea name="message" placeholder="Schrijf je bericht..." required></textarea>
                    <button type="submit">Plaats reactie</button>
                </form>

                <div class="comments-list">
                </div>
            </div>

            <?php
        }
        ?>
    </div>
</body>

<script>var movieId = <?php echo $movieid; ?>;</script>
<script src="assets/js/detials.js"></script>
</html>
