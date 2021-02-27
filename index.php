<?php

require_once(dirname(__FILE__) . "/src/api.php");
$api = new api();

if (count($_GET) > 0) {
  echo $api->processShortRequest($_GET);
  return;
}

/*
 * Convenience queries for Savannah Bugs.
 */
$queries = [
  ['sum'   => true,
   'label' => 'Octave 6',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&Release=6',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=221&advsrch=1&resolution_id%5B%5D=0&bug_group_id%5B%5D=0&status_id%5B%5D=1&priority%5B%5D=0&severity%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&category_id%5B%5D=113&release_id%5B%5D=173&release_id%5B%5D=174&platform_version_id%5B%5D=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=25&history_date_monthfd=2&history_date_yearfd=2021&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Severity >= 4',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&Severity=4,5,6&Status!=Wont',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=7&severity%5B%5D=8&severity%5B%5D=9&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Crashes',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&ItemGroup=Segfault&Status!=Wont',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=101&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Configuration and Build System',
   'api'   => 'TrackerID=bugs&Category=Configuration&Status!=Wont',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=103&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Build Failures',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&ItemGroup=Build Failure&Status!=Wont',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=105&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Regressions',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&ItemGroup=Regression&Status!=Wont',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=1&resolution_id%5B%5D=102&resolution_id%5B%5D=103&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=4&resolution_id%5B%5D=11&resolution_id%5B%5D=8&resolution_id%5B%5D=6&resolution_id%5B%5D=7&resolution_id%5B%5D=2&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=111&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Bugs with Patch submitted',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&Status=Submitted',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=102&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Bugs with Patch reviewed',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&Status=Reviewed',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=103&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Ready for test',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&Status=Ready',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=10&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=10&history_date_monthfd=12&history_date_yearfd=2019&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => false,
   'label' => 'Bugs not fixed for the next Octave release (OPEN bugs marked as POSTPONED)',
   'api'   => 'TrackerID=bugs&Category!=Forge,website&Status=Postponed',
   'url'   => 'https://savannah.gnu.org/bugs/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=4&submitted_by%5B%5D=0&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=110&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=104&category_id%5B%5D=105&category_id%5B%5D=106&category_id%5B%5D=107&category_id%5B%5D=103&category_id%5B%5D=114&category_id%5B%5D=112&category_id%5B%5D=109&bug_group_id%5B%5D=0&severity%5B%5D=0&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=26&history_date_monthfd=10&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => true,
   'label' => 'Patch tracker patches submitted',
   'api'   => 'TrackerID=patch&Category!=Forge,website'
              . '&Status=None,Ready,Progress,Info,Bug',
   'url'   => 'https://savannah.gnu.org/patch/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=100&resolution_id%5B%5D=10&resolution_id%5B%5D=9&resolution_id%5B%5D=8&resolution_id%5B%5D=101&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=103&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=18&history_date_monthfd=1&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options'],
  ['sum'   => false,
   'label' => 'Patches not included for the next Octave release (OPEN items marked as POSTPONED)',
   'api'   => 'TrackerID=patch&Category!=Forge,website&Status=Postponed',
   'url'   => 'https://savannah.gnu.org/patch/index.php?go_report=Apply&group=octave&func=browse&set=custom&msort=0&report_id=101&advsrch=1&status_id%5B%5D=1&resolution_id%5B%5D=4&assigned_to%5B%5D=0&category_id%5B%5D=100&category_id%5B%5D=101&category_id%5B%5D=102&category_id%5B%5D=103&priority%5B%5D=0&summary=&details=&sumORdet=0&history_search=0&history_field=0&history_event=modified&history_date_dayfd=9&history_date_monthfd=2&history_date_yearfd=2020&chunksz=100&spamscore=5&boxoptionwanted=1#options']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>GNU Octave -- Release Burn Down Chart</title>
  <script src="src/scripts.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="src/style.css">
</head>
<body>

<h1>GNU Octave -- Release Burn Down Chart</h1>

<button type="button" onclick="openAll()">show all details</button>
<button type="button" onclick="closeAll()">hide all details</button>
&nbsp;&nbsp;<a href="editor.html">API Editor</a>

<?php
function createItem($api, $query) {
  $commonAPIParams =
    'Action=get&Format=HTMLCSS&OpenClosed=open&Columns='
    . 'ItemID,Title,SubmittedOn,LastComment,Category,Severity,'
    . 'Priority,ItemGroup,Status,AssignedTo,Release,OperatingSystem';

  $apiRequest  = $commonAPIParams . '&' . $query['api'];
  $resultTable = $api->processRequestString($apiRequest);
  $count = max(substr_count($resultTable, '<tr') - 1, 0);
  $output = "<b>($count)</b> " . $query['label']
    . " &nbsp; <a href=\"" . $query['url'] .   "\">[Savannah]</a>"
    . " &nbsp; <a href=\"editor.html?$apiRequest\">[API editor]</a>";
  $output = "<summary>$output</summary>";
  $output = "<details>$output $resultTable</details>";

  return [$count, $output];
}

foreach ([['Bug', '/bugs/'], ['Patch', '/patch/']] as $tracker) {
  $sum = 0;
  $output = '';
  foreach ($queries as $query) {
    if (strpos($query['url'], $tracker[1]) !== false) {
      list($count, $newOutput) = createItem($api, $query);
      $output .= $newOutput;
      if ($query['sum']) {
        $sum += $count;
      }
    }
  }
  echo '<h2>' . $tracker[0] . " Tracker ($sum)</h2>$output";
}
?>

<div id="footer">
  <p>
    Get the source code of this page on
    <a href="https://github.com/gnu-octave/SavannahAPI">GitHub</a>.
  </p>
</div>

</body>
</html>
