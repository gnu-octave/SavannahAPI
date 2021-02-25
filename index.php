<?php
$page_title = "GNU Octave -- Release Burn Down Chart";

/*
 * Convenience queries for Savannah Bugs.
 */
$queries = [
  ['sum'   => true,
   'label' => 'Octave 6',
   'api'   => ['TrackerID' => 'bugs',
               'Release' => '6'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=221&advsrch=1&resolution_id%5B%5D=0&bug_group_id%5B%5D=0&status_id%5B%5D=1&priority%5B%5D=0&severity%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&category_id%5B%5D=113&release_id%5B%5D=173&release_id%5B%5D=174&platform_version_id%5B%5D=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=25&history_date_monthfd=2&history_date_yearfd=2021&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Severity >= 4',
   'api'   => ['TrackerID' => 'bugs',
               'Severity'  => '4,5,6',
               'Status!'   => 'Wont'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=7&severity%5B%5D=8&severity%5B%5D=9&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Crashes',
   'api'   => ['TrackerID' => 'bugs',
               'ItemGroup' => 'Segfault',
               'Status!'   => 'Wont'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=101&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Configuration and Build System',
   'api'   => ['TrackerID' => 'bugs',
               'Category'  => 'Configuration',
               'Status!'   => 'Wont'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=103&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Build Failures',
   'api'   => ['TrackerID' => 'bugs',
               'ItemGroup' => 'Build Failure',
               'Status!'   => 'Wont'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=105&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Regressions',
   'api'   => ['TrackerID' => 'bugs',
               'ItemGroup'  => 'Regression',
               'Status!'   => 'Wont'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=111&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Bugs with Patch submitted',
   'api'   => ['TrackerID' => 'bugs',
               'Status'    => 'Submitted'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=102&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Bugs with Patch reviewed',
   'api'   => ['TrackerID' => 'bugs',
               'Status'    => 'Reviewed'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=103&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Ready for test',
   'api'   => ['TrackerID' => 'bugs',
               'Status'    => 'Ready'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=10&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => false,
   'label' => 'Bugs not fixed for the next Octave release (OPEN bugs marked as POSTPONED)',
   'api'   => ['TrackerID' => 'bugs',
               'Status'    => 'Postponed'],
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=4&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=26&history_date_monthfd=10&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Patch tracker patches submitted',
   'api'   => ['TrackerID' => 'patch',
               'Status'    => 'None,Ready,Progress,Info,Bug'],
   'url'   => 'https://savannah.gnu.org/patch/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=8&resolution_id%5B%5D=101&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=103&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=18&history_date_monthfd=1&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => false,
   'label' => 'Patches not included for the next Octave release (OPEN items marked as POSTPONED)',
   'api'   => ['TrackerID' => 'patch',
               'Status'    => 'Postponed'],
   'url'   => 'https://savannah.gnu.org/patch/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=4&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=103&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=9&history_date_monthfd=2&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options']
];
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
&nbsp;&nbsp;<a href="editor.html">API Editor</a>

<?php
$commonAPIParams = [
  'Action'     => 'get',
  'Format'     => 'HTMLCSS',
  'OpenClosed' => 'open',
  'Category!'  => 'Forge,website',
  'Columns'    => 'TrackerID,ItemID,Title,SubmittedOn,LastComment,Category,Severity,Priority,ItemGroup,Status,AssignedTo,Release,OperatingSystem,OpenClosed'];

define('USE_API_INCLUDED', true);
require_once("api.php");
$api = new api();

foreach ([['Bug', '/bugs/'], ['Patch', '/patch/']] as $tracker) {
  $sum = 0;
  $output = '';
  foreach ($queries as $query) {
    if (strpos($query['url'], $tracker[1]) !== false) {
      $apiRequest = array_merge($commonAPIParams, $query['api']);
      // Ugly, but happens only once...
      if (array_key_exists('Category', $apiRequest)) {
        unset($apiRequest['Category!']);
      }
      $resultTable = $api->processRequest($apiRequest);
      $count = max (substr_count($resultTable, '<tr') - 1, 0);
      $output .= '<details>';
      $output .= '<summary>';
      $output .= '  <b>(' . $count . ')</b> ' . $query['label'] . ' &nbsp; ';
      $output .= '  <a href="' . $query['url'] . '">[link]</a>';
      $output .= '</summary>';
      $output .= $resultTable;
      $output .= '</details>';
      if ($query['sum']) {
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
    Get the source code of this page on
    <a href="https://github.com/gnu-octave/release-burn-down-chart">GitHub</a>.
  </p>
</div>

</body>
</html>
