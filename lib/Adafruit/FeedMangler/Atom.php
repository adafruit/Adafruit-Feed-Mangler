<?php
namespace Adafruit\FeedMangler;

use Adafruit\FeedMangler\Mangler;

class Atom {

  public static function searchResults ($term) {
    $search_data = Mangler::getSearchData($term);

    if (array_key_exists('results', $search_data))
      $results = $search_data['results'];
    else
      $results = [];

    $feed = new \FeedWriter\ATOM;
    $feed->setTitle('Hackaday IO - ' . $term);
    $feed->setLink(APPLICATION_URL);

    $feed->setChannelElement('updated', date(\DATE_ATOM , time()));
    $feed->setChannelElement('author', ['name' => 'Adafruit Industries Feed Mangler']);
    $feed->setSelfLink(APPLICATION_URL);

    foreach ($results as $result) {
      $item = $feed->createNewItem();
      $item->setAuthor('foo');

      if (isset($result['url']))
        $item->setLink($result['url']);

      if (isset($result['updated']))
        $item->setDate( date('r', $result['updated']) );

      if (isset($result['description']))
        $item->setContent($result['description']);

      if (isset($result['summary']))
        $item->setTitle($result['summary']);

      $feed->addItem($item);
    }

    return $feed->generateFeed();
  }

}
