<?php
require 'app.rc.php';

use \Doctrine\Common\Cache\FilesystemCache;
use \SparkLib\Fail;

$search_data = getSearchData('adafruit');
$results = $search_data['results'];
// print_r($results); exit;

header('Content-type: application/atom+xml');

$feed = new \FeedWriter\ATOM;
$feed->setTitle('Hackaday IO - adafruit');
$feed->setLink(APPLICATION_URL);

$feed->setChannelElement('updated', date(\DATE_ATOM , time()));
$feed->setChannelElement('author', ['name' => 'Adafruit Industries / Hackaday']);
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

print $feed->generateFeed();

// And now, some functions.

function getSearchData ($term) {
  // example: https://api.hackaday.io/v1/search?api_key=7yRgvQsCczOev&search_term=test
  $query = http_build_query([
    'api_key'     => API_KEY,
    'search_term' => $term,
  ]);
  $search_url = 'https://api.hackaday.io/v1/search?' . $query;

  $raw_search_json = getOrSetCache("search-{$term}", function () use ($search_url) {
    return file_get_contents($search_url);
  });

  return json_decode($raw_search_json, true /* use assoc array */);
}

/**
 * Get a cached value by key, or stash the value returned from $callback
 * under that key and return it.
 */
function getOrSetCache ($key, $callback, $expire = 600) {
  $cache = new FilesystemCache('/tmp');
  $cache->setNamespace('hackaday_api_cache');

  // Juuuuuuust in case anyone gets clever with input - I don't know that I
  // especially trust this cache implementation not to dump user input onto the
  // filesystem:
  $hashed_key = hash('sha256', $key);

  if ($cache->contains($hashed_key)) {
    return $cache->fetch($hashed_key);
  }

  $callback_result = call_user_func($callback);
  $cache->save($hashed_key, $callback_result, $expire);

  return $callback_result;
}
