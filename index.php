<?php
require 'app.rc.php';

use \SparkLib\Fail;
use Adafruit\FeedMangler\Mangler;

$app = new \Slim\Slim;

$feed_responder = function ($term) use ($app) {
  $search_data = Mangler::getSearchData($term);
  $results = $search_data['results'];

  $feed = new \FeedWriter\ATOM;
  $feed->setTitle('Hackaday IO - ' . $term);
  $feed->setLink(APPLICATION_URL);

  $feed->setChannelElement('updated', date(\DATE_ATOM , time()));
  $feed->setChannelElement('author', ['name' => 'Adafruit Industries Feed Mangler']);
  $feed->setSelfLink(APPLICATION_URL);

  foreach ($results as $result) {
    $item = $feed->createNewItem();
    $item->setTitle($result['summary']);
    $item->setAuthor('foo');
    $item->setLink($result['url']);
    $item->setDate( date('r', $result['updated']) );
    $item->setContent($result['description']);
    $feed->addItem($item);
  }

  $app->response->headers->set('Content-Type', 'application/atom+xml');
  print $feed->generateFeed();
};

$app->get('/search/:term', $feed_responder);

$app->get('/', function () use ($feed_responder) {
  $feed_responder('adafruit');
});

$app->run();
