<?php
namespace Adafruit\FeedMangler;

use \Doctrine\Common\Cache\FilesystemCache;

class Mangler {

  public static function getSearchData ($term) {
    // example: https://api.hackaday.io/v1/search?api_key=7yRgvQsCczOev&search_term=test
    $query = http_build_query([
      'api_key'     => API_KEY,
      'search_term' => $term,
    ]);
    $search_url = 'https://api.hackaday.io/v1/search?' . $query;

    $raw_search_json = self::getOrSetCache("search-{$term}", function () use ($search_url) {
      return file_get_contents($search_url);
    });

    return json_decode($raw_search_json, true /* use assoc array */);
  }

  /**
   * Get a cached value by key, or stash the value returned from $callback
   * under that key and return it.
   */
  protected static function getOrSetCache ($key, $callback, $expire = 600) {
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

}
