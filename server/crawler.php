<?php

require_once("config.php");

/**
 * Getting data from Savannah.
 */
class crawler
{
  /**
   * Look at the Savannah mailing list archives, which items have been updated.
   *
   * @param tracker see `CONST::TRACKER` for possible values.
   *
   * @param lastTimestamp last timestamp to check to speed up the search.
   *
   * @returns an array of valid item IDs of a specific Savannah tracker.
   */
  public function crawlUpdatedItems($tracker, int $lastTimestamp)
  {
    // Day resolution.
    $finalYear  = date("Y", $lastTimestamp);
    $finalMonth = date("m", $lastTimestamp);
    $finalDay   = date("d", $lastTimestamp);
    $lastTimestamp = mktime(0,0,0,$finalMonth,$finalDay,$finalYear);
    DEBUG_LOG("Crawl updated items from '$tracker' tracker
               since '$finalYear-$finalMonth'.");

    $ids = array();
    $url = CONFIG::TRACKER_MAIL_ARCHIVE[$tracker];
    $pattern = ($tracker == 'bugs') ? '!\[bug #(\d*?)\]!'
                                    : '!\[patch #(\d*?)\]!';

    for ($Y = date("Y"), $M = date("m");
         (($Y > $finalYear) || ($M >= $finalMonth)); $M--) {
      if ($M <= 0) {
        $M = 12;
        $Y--;
      }
      $M = sprintf('%02d', $M);
      DEBUG_LOG("--> Crawl $Y-$M.");

      $doc = new DOMDocument;
      $doc->preserveWhiteSpace = false;
      $doc->loadHTMLFile("$url$Y-$M");
      $xpath = new DOMXpath($doc);
      foreach ($xpath->query('//body/ul/li') as $day) {
        $day  = $day->nodeValue;
        $date = explode("$Y", $day, 2);
        $day  = $date[1];
        $date = (int) strtotime($date[0] . "$Y");
        if ($date < $lastTimestamp) {
          continue;
        }
        preg_match_all($pattern, $day, $matches);
        $newIDs = array_map('intval', array_unique($matches[1]));
        sort($newIDs);
        $ids = array_merge ($ids, $newIDs);
      }
    }
    $ids = array_unique($ids);
    sort($ids);
    if (count($ids) > 0) {
      DEBUG_LOG("----> Found " . count($ids) . " updated items
                    from ID '" . $ids[0]     . "'
                         to '" . end($ids)   . "'");
    } else {
      DEBUG_LOG("----> No updated items found.");
    }

    return $ids;
  }


  /**
   * Get all valid item IDs of a specific Savannah tracker.
   *
   * @param tracker see `CONST::TRACKER` for possible values.
   *
   * @param lastID last item ID to check to speed up the search.
   *
   * @returns an array of valid item IDs larger than @p lastID.
   */
  public function crawlNewItems($tracker, int $lastID)
  {
    DEBUG_LOG("Crawl new items from '$tracker' tracker until ID '$lastID'.");

    $offset       = 0;
    $num_of_items = 1;
    $lastIDfound  = PHP_INT_MAX;
    $ids = array();
    $url = CONFIG::BASE_URL . "/$tracker/index.php"
                            . "?group=" . CONFIG::GROUP['id']
                            . "&status_id=0"
                            . "&chunksz=" . CONFIG::CHUNK_SIZE;

    while (($offset < $num_of_items) && ($lastIDfound > $lastID)) {
      DEBUG_LOG("--> Crawl $num_of_items items, offset=$offset.");
      $doc = new DOMDocument;
      $doc->preserveWhiteSpace = false;
      $doc->loadHTMLFile("$url&offset=$offset");

      // Watching out for a string like "9027 matching items - Items 1 to 50",
      // where "9027" should be the total number of project bugs.
      $id_counts = $doc->getElementsByTagName('h2');
      if ($id_counts->length > 1) {
        preg_match_all('!\d+!', $id_counts->item(0)->nodeValue, $matches);
        $offset       = (int) $matches[0][2];  // prepare for next loop
        $num_of_items = (int) $matches[0][0];
      }

      // Find IDs on current page
      $newIDs = array();
      $xpath = new DOMXpath($doc);
      foreach ($xpath->query('//table[@class="box"]/tr/td[1]') as $id) {
        $id = (int) substr($id->nodeValue, 3);
        if ($id > $lastID) {
          array_push($newIDs, $id);
        }
        $lastIDfound = $id;
      }
      if (count($newIDs) > 0) {
        DEBUG_LOG("----> Found IDs from '" . $newIDs[0]
                           . "' down to '" . end($newIDs) . "'");
      } else {
        DEBUG_LOG("----> No new items found.");
      }
      $ids = array_merge($ids, $newIDs);
    }

    return $ids;
  }


  /**
   * Crawl all possible information about a specific Savannah item.
   *
   * @param tracker see `CONST::TRACKER` for possible values.
   *
   * @param id item ID on the respective tracker.
   *
   * @returns `array($item, $discussion)`, where
   *          `$item` is associative array with fields given in the "database
   *                  column" of `CONST::ITEM_DATA`.
   *          `$discussion` is associative array with fields given in the
   *                        "database column" of `CONST::DISCUSSION_DATA`
   *          or `false` on error.
   */
  public function crawlItem($tracker, int $id)
  {
    $keys = array_column(array_values(CONFIG::ITEM_DATA), 0);
    $item = array_fill_keys($keys, '');
    $item['ItemID']    = $id;
    $item['TrackerID'] = array_search($tracker, CONFIG::TRACKER);

    $doc = new DOMDocument;
    $doc->preserveWhiteSpace = false;
    $doc->loadHTMLFile(CONFIG::BASE_URL . "/$tracker/index.php?$id");

    // Check if bug belongs to the project group.
    $project = $doc->getElementsByTagName('title');
    if (($project->length < 1)
        || (strncmp($project[0]->nodeValue, CONFIG::GROUP['name'],
                    strlen(CONFIG::GROUP['name'])) !== 0)) {
      DEBUG_LOG("--> '$tracker' item ID $id does not belong to project group '"
                . CONFIG::GROUP['name'] . "'.");
      return false;
    }

    // Extract title.
    $title = $doc->getElementsByTagName('h1');
    if ($title->length > 1) {
      $item['Title'] = explode(': ', $title[1]->nodeValue, 2)[1];
    } else {
      $item['Title'] = '???';
    }

    // Match key value pairs in remaining metadata.
    $xpath = new DOMXpath($doc);
    $metadata = $xpath->query('//form/table[1]');
    if ($metadata->length > 0) {
      $metadata = explode("\n", $metadata[0]->nodeValue);
      foreach ($metadata as $idx=>$key) {
        $key = trim($key, " \u{a0}");  // remove space and fixed space
        if (array_key_exists($key, CONFIG::ITEM_DATA)) {
          $value = $metadata[$idx + 1];
          switch ($key) {
            case 'Submitted by:':
              $value = trim(htmlspecialchars($value));
              break;
            case 'Assigned to:':
              $value = trim(htmlspecialchars($value));
              break;
            case 'Submitted on:':          // TIMESTAMP
              $value = strtotime($value);
              break;
            case 'Open/Closed:':           // INTEGER
              $value = array_search(strtolower($value), CONFIG::ITEM_STATE);
              break;
          }
          $item[CONFIG::ITEM_DATA[$key][0]] = $value;
        }
      }
    }

    // Extract discussion for full-text search.
    $discussion = array();
    $table = $xpath->query('//table[@class="box" and position()=1]');
    if ($table->length > 0) {
      $maxDate = 0;
      foreach ($xpath->query('tr', $table[0]) as $comment) {
        $text   = $xpath->query('.//div'   , $comment);
        $date   = $xpath->query('./td[1]/a', $comment);
        $author = $xpath->query('./td[2]/a', $comment);
        if ($author->length > 0) {
          $author = htmlspecialchars($author[0]->nodeValue);
        } else {
          $author = 'Anonymous';
        }
        if ($date->length > 0) {
          $date = strtotime(explode(',', $date[0]->nodeValue)[0]);
        } else {
          $date = 0;
        }
        if ($text->length > 0) {
          $text = htmlspecialchars($this->DOMinnerHTML($text[0]));
        } else {
          $text = '???';
        }
        if ($date !== 0) {
          $maxDate = max($maxDate, $date);
          $new_item["Date"]   = $date;
          $new_item["Author"] = $author;
          $new_item["Text"]   = $text;
          array_push($discussion,$new_item);
        }
      }
      $item["LastComment"] = $maxDate;
    }

    // Extract number of attached files.
    $attachments = $xpath->query('//div[@id="hidsubpartcontentattached"]'
                       . '//div[@class="boxitem" or @class="boxitemalt"]');
    $item['AttachedFiles']     = $attachments->length;
    $item['AttachedFileNames'] = '';
    foreach ($attachments as $attachment) {
      $a = $xpath->query('./a', $attachment);
      $item['AttachedFileNames'] .= $a[0]->textContent . ', ';
    }

    DEBUG_LOG("----> Found " . $item['AttachedFiles'] . " attached files.");

    return array($item, $discussion);
  }


  /**
   * Helper function to retrieve HTML from a DOM node.
   *
   * See https://stackoverflow.com/a/2087136/3778706
   */
  private function DOMinnerHTML(DOMNode $element)
  {
    $innerHTML = "";
    $children  = $element->childNodes;

    foreach ($children as $child) {
      $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    return $innerHTML;
  }
}

?>
