<?php
require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

$response = $client->request('GET', 'https://api.themoviedb.org/3/movie/414', [
  'headers' => [
    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJmYTMyM2I4MWIwMmI3NTk0YmZjNTcxYjdmYzQyMGI4NyIsIm5iZiI6MTczMzkwOTQzMy43MzYsInN1YiI6IjY3NTk1YmI5YWQ5MTg3MjFkMGRlZTQ1ZSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.f7vsQIqKz5TaH_Rc2iFS-KMZWHJ5ScOWKzl25o6Eocg',
    'accept' => 'application/json',
  ],
]);

echo $response->getBody();