<?php
require '../app.rc.php';

use \SparkLib\Fail;

$app = new \Slim\Slim;

$app->response->headers->set('Content-Type', 'application/atom+xml');

$app->get('/search/:term', function ($term) {
  print \Adafruit\FeedMangler\Atom::searchResults($term);
});

$app->get('/', function () {
  print \Adafruit\FeedMangler\Atom::searchResults('adafruit');
});

$app->run();
