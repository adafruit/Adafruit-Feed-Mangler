<?php
require '/var/www-php/Adafruit-Feed-Mangler/app.rc.php';

use \SparkLib\Fail;

$app = new \Slim\Slim;
$app->config('debug', true);

$app->error(function (\Exception $e) use ($app) {
  print "something broke";
});

$app->get('/', function () use ($app) {
  $app->response->headers->set('Content-Type', 'application/atom+xml');
  $term = 'adafruit';
  if ($app->request->get('search')) {
    $term = $app->request->get('search');
  }
  print \Adafruit\FeedMangler\Atom::searchResults($term);
});

$app->run();
