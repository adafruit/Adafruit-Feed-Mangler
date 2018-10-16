<?php
require '/var/www-php/Adafruit-Feed-Mangler/app.rc.php';

$app = new \Slim\Slim;
$app->config('debug', false);

$app->error(function (\Exception $e) use ($app) {
  print "something broke";
});

$app->get('/', function () use ($app) {

  $term = 'adafruit';
  if ($app->request->get('search')) {
    $term = $app->request->get('search');
  }

  if ($app->request->get('type') === 'json') {
    $app->response->headers->set('Content-Type', 'application/json');
    print \Adafruit\FeedMangler\Mangler::getSearchJson($term);
  } else {
    $app->response->headers->set('Content-Type', 'application/atom+xml');
    print \Adafruit\FeedMangler\Atom::searchResults($term);
  }

});

$app->run();
