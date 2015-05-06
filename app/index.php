<?php
require '../app.rc.php';

use \SparkLib\Fail;

$app = new \Slim\Slim;


$app->get('/search/:term', function ($term) use ($app) {
  $app->response->headers->set('Content-Type', 'application/atom+xml');
  print \Adafruit\FeedMangler\Atom::searchResults($term);
});

$app->get('/', function () use ($app) {
  $app->response->headers->set('Content-Type', 'application/atom+xml');
  print \Adafruit\FeedMangler\Atom::searchResults('adafruit');
});

$app->get('/json', function () use ($app) {
  $app->response->headers->set('Content-Type', 'text/html');
  print '<pre>' . print_r(\Adafruit\FeedMangler\Mangler::getSearchData('adafruit'), 1) . '</pre>';
});

$app->run();
