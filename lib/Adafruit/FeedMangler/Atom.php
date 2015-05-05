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
      // print_r($result);
      $item = $feed->createNewItem();
      $item->setAuthor('foo');

      if (isset($result['url']))
        $item->setLink($result['url']);

      if (isset($result['updated']))
        $item->setDate( date('r', $result['updated']) );

      $content_html = '';

      if (isset($result['summary']))
        $content_html .= '<p><i>' . $result['summary'] . '</i></p>';

      if (isset($result['description']))
        $content_html .= '<p>' . nl2br($result['description']) . '</p>';

      if (isset($result['skulls'])) {
        $s = $result['skulls'];
        if ($s > 100)
          $content_html .= "<p>☠ x {$s}</p>";
        elseif ($s > 0)
          $content_html .= "<p>" . str_repeat('☠', $s);
      }

      if (strlen($content_html))
        $item->setContent($content_html);

      if (isset($result['name']))
        $item->setTitle($result['name']);

      $feed->addItem($item);
    }

    return $feed->generateFeed();
  }

}
