<?php
$cache_file = 'savannah.cache.json';
$cache_max_age = 2 * 60;  // seconds

/*
 *  Helper functions
 */

function fixTableHead($tab) {
  $tab = preg_replace('/<a class.*?>(.*?)<\/a>/', '$1', $tab);
  $tab = str_replace(
    ' <img class="icon" src="/images/Savannah.theme/arrows/up.png" alt="up" border="0">',
    '', $tab);
  return $tab;
}

function fixTableURLs($tab, $url) {
  $tab = str_replace('href="?', 'href="'.strtok($url , '?').'?', $tab);
  $tab = str_replace('href="/',
                     'href="https://savannah.gnu.org/',
                     $tab);
  return $tab;
}

function extractTableFromURL($url) {
  $doc = new DOMDocument;
  $doc->preserveWhiteSpace = false;
  $doc->loadHTMLFile($url);
  $tab = $doc->getElementsByTagName('table');  // Result table
  if ($tab->length < 2) {
    return "No elements.";
  } else {
    return fixTableURLs(fixTableHead($doc->saveHTML($tab[1])), $url);
  }
}

function countTableItems($tab) {
  return max (substr_count($tab, '<tr') - 1, 0);  // minus table head
}

/*
 * Load from cache if possible.
 */
if (file_exists($cache_file)
    && (time() - filemtime($cache_file) < $cache_max_age)) {
  $queries = json_decode(file_get_contents($cache_file));
} else {
  $queries = array(
    array(
      'Bugs marked as Crash',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=101&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Bugs marked Configuration and Build System',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=103&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Bugs marked as Build Failure',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=105&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Bugs with severity >= 4',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=7&severity%5B%5D=8&severity%5B%5D=9&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Bugs marked as regressions',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=111&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Bugs with Patch submitted',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=102&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Bugs with Patch reviewed',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=103&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Bugs with Ready for test',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=10&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Bugs not fixed for for the next Octave release (OPEN bugs marked as WON\'T FIX )',
      'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=3&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Patch tracker patches submitted',
      'https://savannah.gnu.org/patch/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=8&resolution_id%5B%5D=101&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=103&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=18&history_date_monthfd=1&history_date_yearfd=2020&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    ),
    array(
      'Patches not included for the next Octave release (OPEN items marked as POSTPONED)',
      'https://savannah.gnu.org/patch/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=4&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=103&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=9&history_date_monthfd=2&history_date_yearfd=2020&chunksz=50&spamscore=5&boxoptionwanted=1#options'
    )
  );
  foreach ($queries as $key => $query) {
    $queries[$key][2] = extractTableFromURL($query[1]);
  }
  file_put_contents($cache_file, json_encode($queries), LOCK_EX);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>GNU Octave -- Release Burndown Chart</title>
</head>
<body>

<h1>GNU Octave -- Release Burndown Chart</h1>

<h2>Bug Tracker</h2>

<?php
foreach ($queries as $key => $query) {
  if (strpos($query[1], '/bugs/') !== false) {
    $count = countTableItems($query[2]);
    echo '<details>';
    echo '<summary><b>(' . $count . ')</b> ' . $query[0] . '</summary>';
    echo $query[2];
    echo '</details>';
  }
}
?>

<h2>Patch Tracker</h2>

<?php
foreach ($queries as $key => $query) {
  if (strpos($query[1], '/patch/') !== false) {
    $count = countTableItems($query[2]);
    echo '<details>';
    echo '<summary><b>(' . $count . ')</b> ' . $query[0] . '</summary>';
    echo $query[2];
    echo '</details>';
  }
}
?>

<div id="cache_update">
  Update cache in
  <b><?php echo $cache_max_age - time() + filemtime($cache_file) ?></b>
  seconds.
</div>

</body>
</html>
