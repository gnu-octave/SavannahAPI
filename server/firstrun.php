<?php

/**
 * Script to bootstrap the database of SavannahAPI.
 * Best run this script directly on your web server, e.g.
 *
 *   php firstrun.php 2>&1 | tee firstrun.log.txt
 *
 * It requires to find an (empty) file called "FIRSTRUN" in the current
 * directory for protection of misuse.  Create and delete this file with
 *
 *   touch FIRSTRUN
 *   rm -f FIRSTRUN
 */

if (!file_exists('FIRSTRUN')) {
  die("Access denied.\n");
}

function DEBUG_LOG($str) {
  echo("$str\n");
  flush();
}

require_once("config.php");
require_once("crawler.php");
require_once("db.php");

$all_time_start = microtime(true);
foreach (CONFIG::TRACKER as $tracker) {
  $db = DB::getInstance();
  $crawler = new crawler();

  $time_start = microtime(true);
  $ids = $crawler->crawlNewItems($tracker, 0);
  $time_end   = microtime(true);
  $time = substr($time_end - $time_start, 0, 6);
  DEBUG_LOG("Found '" . count($ids) . "' items on '$tracker' in $time seconds.");
  $ids = array_map('intval', array_unique($ids));
  sort($ids);  // oldest first
  foreach ($ids as $id) {
    if ($id === 0) {
      die("Invalid ItemID for TrackerID '$tracker'.  Stopping.");
    }
    DEBUG_LOG("Processing item ID '$id' from '$tracker'.");
    list($item, $discussion) = $crawler->crawlItem($tracker, $id);
    if (isset($item) && isset($discussion)) {
      $db->update($item, $discussion);
    } else {
      die("Invalid ItemID '$id' for TrackerID '$tracker'.  Stopping.");
    }
  }
}

$all_time_end = microtime(true);
$time = substr($all_time_end - $all_time_start, 0, 6);
DEBUG_LOG("First run was successful after $time seconds.  "
          . "Do not forget to delete the 'rm -f FIRSTRUN' file.");

?>
