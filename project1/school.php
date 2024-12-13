<?php
include("config.php");
include("reactions.php");

require 'vendor/autoload.php';

use YoHang88\LetterAvatar\LetterAvatar;
use Carbon\Carbon;

$getReactions = Reactions::getReactions();
//uncomment de volgende regel om te kijken hoe de array van je reactions eruit ziet
// echo "<pre>".var_dump($getReactions)."</pre>";


if(!empty($_POST)){

    //dit is een voorbeeld array.  Deze waardes moeten erin staan.
    $postArray = [
        'name' => "Ieniminie",
        'email' => "ieniminie@sesamstraat.nl",
        'message' => "Geweldig dit"
    ];

    $setReaction = Reactions::setReaction($postArray);

    if(isset($setReaction['error']) && $setReaction['error'] != ''){
        prettyDump($setReaction['error']);
    }
    

}

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
    <iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ?si=twI61ZGDECBr4ums" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

    <h2>Hieronder komen reacties</h2>
    <p>Maak hier je eigen pagina van aan de hand van de opdracht</p>
    <?php
    echo "<div class='reactions'>";
    foreach($getReactions as $reaction){
        echo "<div class='reaction'>";
        echo "<img id='avatar' src='".new LetterAvatar($reaction['name'])."' alt='avatar'>";
        echo "<div>";
        echo "<div class='name-time'>";
        echo "<p id='name'>@".$reaction['name']."</p>";
        echo "<p id='time-ago'>".Carbon::parse($reaction['date_added'])->locale('nl-NL')->diffForHumans()."</p>";
        echo "</div>";
        echo "<p id='message'>".$reaction['message']."</p>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";    
    ?>
</body>
</html>

<?php
$con->close();
?>