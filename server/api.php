<?php

require_once("config.php");
require_once("crawler.php");
require_once("db.php");
require_once("formatter.php");

class api
{
  /**
   * Valid keys and data types for API action requests.
   */
  private $apiActions = null;
  private $formats = ['HTML', 'HTMLCSS', 'JSON', 'JSONFULL', 'CSV'];

  /**
   * Constructor.
   */
  function __construct()
  {
    // Define an associative array of valid API actions.
    $columnNames = array_column(array_values(CONFIG::ITEM_DATA), 0);

    $apiActions['get'] = array_fill_keys($columnNames, null);
    $apiActions['get']['TrackerID']    = CONFIG::TRACKER;
    $apiActions['get']['OpenClosed']   = CONFIG::ITEM_STATE;
    foreach ($apiActions['get'] as $key => $value) {  // Allow negations.
      if (($key === 'SubmittedOn') || ($key === 'LastComment')) {
        continue;  // Negations make no sense here.
      }
      $apiActions['get']["$key!"] = $value;
    }
    $apiActions['get']['Format']       = $this->formats;
    $apiActions['get']['Limit']        = null;
    $apiActions['get']['OrderBy']      = $columnNames;
    array_push($columnNames, 'UpdateCallback');
    $apiActions['get']['Columns']      = $columnNames;
    $apiActions['get']['Keywords']     = null;

    // Allow negations for inverted order.
    foreach ($apiActions['get']['OrderBy'] as $value) {
      array_push($apiActions['get']['OrderBy'], "!$value");
    }
    $apiActions['update']['TrackerID'] = CONFIG::TRACKER;
    $apiActions['update']['ItemID']    = null;

    $this->apiActions = $apiActions;
  }


  /**
   * Process an API request.
   *
   * @param requestStringUnfiltered a string like `Action=get&ItemID=5`.
   *
   * @returns a string containing the result of the web request.
   *          Error messages are JSON encoded with the fields:
   *          "state"  : one of "success", "error", "warning", "info"
   *          "message": string with information
   */
  public function processRequestString($requestStringUnfiltered)
  {
    $requestUnfiltered = array();
    foreach (explode('&', $requestStringUnfiltered) as $param) {
      list($key, $value) = explode('=', $param);
      $requestUnfiltered[$key] = $value;
    }
    return $this->processRequest($requestUnfiltered);
  }


  /**
   * Process an API request.
   *
   * @param requestUnfiltered an array like created from `$_GET`.
   *
   * @returns a string containing the result of the web request.
   *          Error messages are JSON encoded with the fields:
   *          "state"  : one of "success", "error", "warning", "info"
   *          "message": string with information
   */
  public function processRequest($requestUnfiltered)
  {
    $request = $this->validateRequest($requestUnfiltered);
    if (is_string($request)) {
      die ($this->JSON("error", $request));
    }

    switch ($request['Action']) {
      case 'update':
        return $this->actionUpdate($request);
        break;
      case 'get':
        return $this->actionGet($request);
        break;
      default:
        die($this->JSON("error", "'action' value must be one of {update|get}."));
    }
  }


  /**
   * Validate request parameters.
   *
   * In PHP $_GET array keys should be unique and the rightmost key-value
   * pair is chosen.  All parameters are case insensitive.
   *
   * @param request an array like created from `$_GET`.
   *
   * @return a valid API request otherwise a string with an error message.
   */
  private function validateRequest($request)
  {
    // Sanitize all user input.
    array_walk_recursive($request, function (&$value) {
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
      });

    // Validate API action.
    if (!array_key_exists('Action', $request)) {
      return "No parameter key 'Action' specified.";
    }
    $keys = array_keys($this->apiActions);
    if (!in_array($request['Action'], $keys)) {
      return "Parameter 'Action' value must be one of
              {" . implode(',', $keys) . "}.";
    }
    $validRequest['Action'] = $request['Action'];

    // Validate remaining parameters.
    $validParameters = $this->apiActions[$request['Action']];
    foreach ($request as $key => $value) {
      // Validate key.
      if ($key === 'Action') {
        continue;
      }
      if (!in_array($key, array_keys($validParameters))) {
        return "Unknown parameter key '$key'
                for 'Action=" . $request['Action'] . ".'
                Valid parameter keys are:
                {" . implode('=, ', array_keys($validParameters)) . "=}.";
      }
      if ((substr($key, -1) === '!')
          && (array_key_exists(substr($key, 0, -1), $request))) {
        return "Parameter key '" . substr($key, 0, -1) . "'
                and it's negation '$key'
                in one request are not supported.";
      }

      // Validate value(s).
      // Separate values by ',' without empty elements.
      $values = explode(',', $request[$key]);
      if (!is_array($values)
          || (count($values) === 0)
          || (count($values) !== count(array_filter($values)))) {
        return "Parameter key '$key': Invalid or empty values given.";
      }
      // Validate individual values, if list of valid values is given.
      if (is_array($validParameters[$key])) {
        foreach ($values as $value) {
          if (!in_array($value, $validParameters[$key])) {
            return "Unknown parameter value for '$key'.
                    Valid parameter keys are:
                    {" . implode(', ', $validParameters[$key]) . "}.";
          }
        }
      }
      switch ($key) {
        case 'Format':
        case 'Limit':
          if (count($values) !== 1) {
            return "Parameter key '$key' allows only exact one value.";
          }
          if (($key == 'Limit') && (intval($values[0]) <= 0)) {
            return "Parameter key '$key' must be a positive integer.";
          }
          $validRequest[$key] = $values[0];
          break;
        default:
          $validRequest[$key] = $values;
      }
    }

    return $validRequest;
  }


  /**
   * Translate IDs and TIMESTAMPS to human readable strings.
   *
   * @param request a validated array like created from `$_GET`.
   *
   * @returns items formatted as string according to @p format.
   */
  private function actionGet($request)
  {
    $time_start = microtime(true);
    $db = DB::getInstance();
    $items = $db->getItems($request);
    if ($request['Format'] === 'JSONFULL') {
      if (count($items) > CONFIG::MAX_JSON_FULL_EXPORT) {
        die($this->JSON("error",
            "More than <b>" . CONFIG::MAX_JSON_FULL_EXPORT . "</b> items found."
            . "  Please narrow your request or consider downloading the"
            . "  entire SQLite database for advanced processing."));
      }
      foreach ($items as &$item) {
        $item['Discussion'] = $db->getDiscussion($item['TrackerID'],
                                                 $item['ItemID']);
      }
      unset($item);  // see PHP manual foreach!
    }
    $time_end   = microtime(true);
    $time = substr($time_end - $time_start, 0, 6);
    DEBUG_LOG("Found " . count($items) . " item(s) in $time seconds.");
    if (array_key_exists('Columns', $request)) {
      $columns = $request['Columns'];
    } else {
      // Default columns.
      $columns = array_column(array_values(CONFIG::ITEM_DATA), 0);
      if ($request['Format'] === 'HTMLCSS') {
        // Do not show some uninteresting fields.
        unset($columns[array_search('SubmittedBy',    $columns)]);
        unset($columns[array_search('OriginatorName', $columns)]);
        // Show UpdateCallback.
        array_unshift($columns , 'UpdateCallback');
      }
    }
    if (count ($items) <= 0) {
      return $this->JSON("info", "No matching items founds.");
    }
    $fmt = new formatter($items, $columns);
    if (!array_key_exists('Format', $request)) {
      return $fmt->asJSON();
    }
    switch ($request['Format']) {
      case 'HTMLCSS':
        return $fmt->asHTML($columns, true);
        break;
      case 'HTML':
        return $fmt->asHTML($columns);
        break;
      case 'JSON':
      case 'JSONFULL':
        return $fmt->asJSON();
        break;
      case 'CSV':
        return $fmt->asCSV();
        break;
      default:
        die($this->JSON("error", "Invalid format, use one of '"
                        . implode("', '", $validParameters[$key]) . "'."));
        break;
    }
  }


  /**
   * Look for updates on Savannah and the mailing list archive and update the
   * database accordingly.
   *
   * @param request a validated array like created from `$_GET`.
   *
   * @returns JSON encoded string as described in `processRequest`.
   */
  private function actionUpdate($request)
  {
    $requestIDs = array_key_exists('ItemID', $request)
                ? $request['ItemID']
                : array();

    // If no tracker is given, recursive call over all trackers.
    $trackers = (array_key_exists('TrackerID', $request))
              ? $request['TrackerID']
              : CONFIG::TRACKER;

    foreach ($trackers as $tracker) {
      $ids = $requestIDs;
      $trackerID = array_search($tracker, CONFIG::TRACKER);
      if ($trackerID === false) {
        return $this->JSON("error", "Invalid TrackerID '$tracker'.  Stopping.");
      }

      $db = DB::getInstance();
      $crawler = new crawler();

      // If no IDs are specified, look for new or updated items.
      if (count($ids) == 0) {
        // Look for new items.
        $nextLookup = $db->getTimer("crawlNewItems_$tracker")
                    + CONFIG::DELAY["crawlNewItems"] - time();
        if ($nextLookup <= 0) {
          $db->setTimer("crawlNewItems_$tracker", time());
          $lastID = $db->getMaxItemIDFromTracker($trackerID);
          $ids = array_merge($ids, $crawler->crawlNewItems($tracker, $lastID));
        } else {
          return $this->JSON("info",
            "'crawlNewItems_$tracker' delayed for $nextLookup seconds.");
        }

        // Look for update items, only if not much new is to be added.
        if (count($ids) < CONFIG::MAX_CRAWL_ITEMS) {
          $nextLookup = $db->getTimer("crawlUpdatedItems_$tracker")
                      + CONFIG::DELAY["crawlUpdatedItems"] - time();
          if ($nextLookup <= 0) {
            $db->setTimer("crawlUpdatedItems_$tracker", time());
            $lastComment = $db->getMaxLastCommentFromTracker($trackerID);
            $ids = array_merge($ids, $crawler->crawlUpdatedItems($tracker,
                                                                $lastComment));
          } else {
            return $this->JSON("info", "'crawlUpdatedItems_$tracker'
                                       delayed for $nextLookup seconds.");
          }
        } else {
          DEBUG_LOG("'crawlUpdatedItems_$tracker' skipped.");
        }
      } else {
        $nextLookup = $db->getTimer("crawlItem")
                    + CONFIG::DELAY["crawlItem"] - time();
        if ($nextLookup <= 0) {
          $db->setTimer("crawlItem", time());
        } else {
          return $this->JSON("info",
                             "'crawlItem' delayed for $nextLookup seconds.");
        }
        if (count($ids) > CONFIG::MAX_CRAWL_ITEMS) {
          return $this->JSON("error", "'crawlItem' not more than "
            . CONFIG::MAX_CRAWL_ITEMS . " item updates permitted.");
        }
      }

      $ids = array_map('intval', array_unique($ids));
      sort($ids);  // oldest first
      foreach ($ids as $id) {
        if ($id === 0) {
          return $this->JSON("error", "Invalid ItemID found.  Stopping.");
        }
        DEBUG_LOG("--> Update item ID '$id' from '$tracker'.");
        list($item, $discussion) = $crawler->crawlItem($tracker, $id);
        if (isset($item) && isset($discussion)) {
          $db->update($item, $discussion);
        } else {
          return $this->JSON("error",
                             "Invalid ItemID '$id' for TrackerID '$tracker'.");
        }
      }
    }

    return $this->JSON("success",  "Update successful.");
  }


  /**
   * Create a well-formed JSON return string.
   */
  private function JSON($state, $message) {
    return json_encode(["state" => $state, "message" => $message]);
  }
}

?>
