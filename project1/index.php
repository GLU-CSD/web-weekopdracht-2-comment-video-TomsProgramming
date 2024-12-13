<?php
include("config.php");
include("reactions.php");

require 'vendor/autoload.php';

$client = new \GuzzleHttp\Client();

$movies = [];

$page = 1;
if(isset($_GET['pages'])){
    $page = $_GET['pages'];
}

$response = $client->request('GET', 'https://api.themoviedb.org/3/movie/popular?page=' . $page, [
    'headers' => [
      'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJmYTMyM2I4MWIwMmI3NTk0YmZjNTcxYjdmYzQyMGI4NyIsIm5iZiI6MTczMzkwOTQzMy43MzYsInN1YiI6IjY3NTk1YmI5YWQ5MTg3MjFkMGRlZTQ1ZSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.f7vsQIqKz5TaH_Rc2iFS-KMZWHJ5ScOWKzl25o6Eocg',
      'accept' => 'application/json',
    ],
]);

$movies = json_decode($response->getBody(), true)['results'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youtube remake</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="movies">
        <?php
        foreach($movies as $movie){
            echo '<div onclick="clickOnMovie('.$movie['id'].')" class="movie">';
            echo '<h2>' . $movie['title'] . '</h2>';
            echo '<img src="https://image.tmdb.org/t/p/w500' . $movie['poster_path'] . '" alt="' . $movie['title'] . '">';
            echo '<p>' . $movie['overview'] . '</p>';
            echo '</div>';
        }
        ?>
    </div>
    
    <div class="pagination">
        <?php
        if ($page > 1) {
            echo '<a href="?pages=' . ($page - 1) . '">Previous</a>';
        }
        echo '<a href="?pages=' . ($page + 1) . '">Next</a>';
        ?>
    </div>
</body>

<script src="assets/js/app.js"></script>
</html>

<?php
$con->close();
?>