<?php
namespace Adafruit\FeedMangler;

use Adafruit\FeedMangler\Mangler;

class Atom {

  public static function searchResults ($term) {
    $search_data = Mangler::getSearchData($term);

    if (array_key_exists('projects', $search_data))
      $results = $search_data['projects'];
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

      $content_text = '';

      if (isset($result['description']))
        $content_text .= $result['description'];

      if (isset($result['skulls'])) {
        $s = $result['skulls'];
        if ($s > 100)
          $content_text .= "<p>☠ x {$s}</p>";
        elseif ($s > 0)
          $content_text .= "<p>" . str_repeat('☠', $s);
      }
          

      if (strlen($content_text))
        $item->setContent($content_text);

      if (isset($result['summary']))
        $item->setTitle($result['summary']);

      $feed->addItem($item);
    }

    return $feed->generateFeed();
  }

}
