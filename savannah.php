<?php
$page_title = "GNU Octave -- Release Burn Down Chart";
$cache_file = 'savannah.cache.json';
$cache_max_age = 2 * 60;  // seconds

/*
 * Convenience queries for Savannah Bugs.
 */

$queries = array(
  array(
    true,
    'Octave 6.0.9x release candidates',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=221&advsrch=1&resolution_id%5B%5D=0&bug_group_id%5B%5D=0&status_id%5B%5D=1&priority%5B%5D=0&severity%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&category_id%5B%5D=113&release_id%5B%5D=170&release_id%5B%5D=172&platform_version_id%5B%5D=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=26&history_date_monthfd=10&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Severity >= 4',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=7&severity%5B%5D=8&severity%5B%5D=9&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Crashes',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=101&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Configuration and Build System',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=103&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Build Failures',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=105&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Regressions',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=111&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Bugs with Patch submitted',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=102&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Bugs with Patch reviewed',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=103&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Ready for test',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=10&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    false,
    'Bugs not fixed for the next Octave release (OPEN bugs marked as POSTPONED)',
    'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=4&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=26&history_date_monthfd=10&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    true,
    'Patch tracker patches submitted',
    'https://savannah.gnu.org/patch/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=8&resolution_id%5B%5D=101&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=103&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=18&history_date_monthfd=1&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  ),
  array(
    false,
    'Patches not included for the next Octave release (OPEN items marked as POSTPONED)',
    'https://savannah.gnu.org/patch/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=4&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=103&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=9&history_date_monthfd=2&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options'
  )
);


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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo $page_title; ?></title>
  <script>
  function openAll (){
    var details = document.getElementsByTagName ("details");
    var len = details.length;
    for (var i = 0; i < len; i++) {
      details[i].setAttribute ("open", "true");
    }
  }

  function closeAll (){
    var details = document.getElementsByTagName ("details");
    var len = details.length;
    for (var i = 0; i < len; i++) {
      details[i].removeAttribute ("open");
    }
  }
  </script>
  <style>
  body { padding: 5px; }
  a { color: navy; }
  details {
    margin: 10px;
    border: 1px solid black;
  }
  summary {
    background-color: beige;
    padding: 5px;
  }
  div#footer {
    text-align: center;
    margin: 20px;
  }
  table { border-collapse: collapse; }
  tr:not([class]) { background-color: powderblue; }
  td, th { padding: 5px; }
  /* From https://savannah.gnu.org/css/internal/base.css */
  .priora { background-color: #fff2f2; }
  .priorb { background-color: #ffe8e8; }
  .priorc { background-color: #ffe0e0; }
  .priord { background-color: #ffd8d8; }
  .priore { background-color: #ffcece; }
  .priorf { background-color: #ffc6c6; }
  .priorg { background-color: #ffbfbf; }
  .priorh { background-color: #ffb7b7; }
  .priori { background-color: #ffadad; }
  </style>
</head>
<body>

<h1><?php echo $page_title; ?></h1>

<button type="button" onclick="openAll()">show all details</button>
<button type="button" onclick="closeAll()">hide all details</button>
<a href="savannah.cache.json">Get JSON data</a>

<?php
$trackers = array(
  array('Bug',   '/bugs/'),
  array('Patch', '/patch/')
);

// Load data from cache if possible.
if (file_exists($cache_file)
    && (time() - filemtime($cache_file) < $cache_max_age)) {
  $queries = json_decode(file_get_contents($cache_file));
} else {
  foreach ($queries as $key => $query) {
    $queries[$key][3] = extractTableFromURL($query[2]);
  }
  file_put_contents($cache_file, json_encode($queries), LOCK_EX);
}

foreach ($trackers as $tracker) {
  $sum = 0;
  $output = '';
  foreach ($queries as $query) {
    if (strpos($query[2], $tracker[1]) !== false) {
      $count = countTableItems($query[3]);
      $output .= '<details>';
      $output .= '<summary>';
      $output .= '  <b>(' . $count . ')</b> ' . $query[1] . ' &nbsp; ';
      $output .= '  <a href="' . $query[2] . '">[link]</a>';
      $output .= '</summary>';
      $output .= $query[3];
      $output .= '</details>';
      if ($query[0]) {
        $sum += $count;
      }
    }
  }
  echo '<h2>' . $tracker[0] . ' Tracker (' . $sum . ')</h2>';
  echo $output;
}
?>

<div id="footer">
  <p>
    Savannah cache expires in
    <b><?php echo max($cache_max_age - time() + filemtime($cache_file), 0); ?></b>
    seconds.
  </p>
  <p>
    Get the source code of this page on
    <a href="https://github.com/gnu-octave/release-burn-down-chart">GitHub</a>.
  </p>
</div>

</body>
</html>
