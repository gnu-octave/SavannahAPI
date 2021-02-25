<?php

require_once("config.php");

class formatter
{
  private $items;

  /* From https://savannah.gnu.org/css/internal/base.css */
  private $CSS_COLORS = [
    'open'   => ['#fff2f2', '#ffe8e8', '#ffe0e0', '#ffd8d8', '#ffcece',
                 '#ffc6c6', '#ffbfbf', '#ffb7b7', '#ffadad'],
    'closed' => ['#f5ffeb', '#edffe6', '#eeffe1', '#e0ffd5', '#ccffbb',
                 '#c6ffb9', '#c0ffb2', '#adffa4', '#a0ff9d']
  ];

  /**
   * Constructor.
   */
  public function  __construct($items)
  {
    $this->items = $items;
  }

  /**
   * Translate IDs and TIMESTAMPS to human readable strings.
   *
   * @param item associative array with fields given in the "database column"
   *             of `CONST::ITEM_DATA`.
   *
   * @returns item with all IDs and TIMESTAMPS as human readable strings.
   */
  private function idsToString($item)
  {
    if (array_key_exists('TrackerID', $item)) {
      $item['TrackerID']   = CONFIG::TRACKER[$item['TrackerID']];
    }
    if (array_key_exists('OpenClosed', $item)) {
      $item['OpenClosed']  = CONFIG::ITEM_STATE[$item['OpenClosed']];
    }
    if (array_key_exists('SubmittedOn', $item)) {
      $item['SubmittedOn'] = date(DATE_RFC2822, $item['SubmittedOn']);
    }
    if (array_key_exists('LastComment', $item)) {
      $item['LastComment'] = date(DATE_RFC2822, $item['LastComment']);
    }
    return $item;
  }

  /**
   * Retrieve Savannah css class from item's priority.
   *
   * @param item associative array with fields given in the "database column"
   *             of `CONST::ITEM_DATA`.
   *
   * @returns a string with the css class attribute.
   */
  private function addCSS($item)
  {
    if (!array_key_exists('OpenClosed', $item)
        || !array_key_exists('Priority', $item)) {
      return "";
    }
    // Translate something like "5 - Normal" to "e", etc.
    $color = $this->CSS_COLORS[$item['OpenClosed']]
                              [((int) $item['Priority'][0]) - 1];
    return " style=\"background-color: $color; padding: 5px;\"";
  }


  /**
   * Add HTML URLs to Savannah where possible.
   *
   * Must be called after `idsToString()`.
   *
   * @param item associative array with fields given in the "database column"
   *             of `CONST::ITEM_DATA`.
   *
   * @returns item inserted URLs.
   */
  private function addURLs($item)
  {
    if (array_key_exists('TrackerID', $item)
        && array_key_exists('ItemID', $item)) {
      $id  = $item['ItemID'];
      $url = CONFIG::BASE_URL . '/' . $item['TrackerID'] . "/index.php?$id";

      $item['ItemID'] = "<a href=\"$url\">$id</a>";
      if (array_key_exists('Title', $item)) {
        $item['Title']  = "<a href=\"$url\">" . $item['Title'] . "</a>";
      }
    }
    return $item;
  }


  /**
   * Get HTML representation of a list of items (without discussion).
   *
   * @param columns array of column head names.
   *
   * @param color (default = false) add Savannah compatible css classes.
   *
   * @returns item as HTML string.
   */
  public function asHTML($columns, $color = false)
  {
    $css = ($color) ? ' style="background-color: powderblue; padding: 5px;"'
                    : '';
    $str = "<tr><th$css>" . implode("</th><th$css>", $columns) . '</th></tr>';
    foreach ($this->items as $item) {
      $item = $this->idsToString($item);
      $css = ($color) ? $this->addCSS($item) : '';
      if ($color) {
        $item = $this->addURLs($item);
      }
      $item_str = '';
      foreach ($item as $col) {
        $item_str .= "<td$css>$col</td>";
      }
      $str .= "<tr>$item_str</tr>";
    }
    $css = ($color) ? ' style="border-collapse: collapse;"' : '';
    return "<table$css>$str</table>";
  }

  /**
   * Get JSON representation of a list of items (without discussion).
   *
   * @param items list of associative arrays with fields given in the
   *              "database column" of `CONST::ITEM_DATA`.
   *
   * @returns item as JSON string.
   */
  public function asJSON()
  {
    $items = $this->items;
    foreach ($items as $idx=>$item) {
      $items[$idx] = $this->idsToString($item);
    }
    return json_encode($items);
  }

  /**
   * Get CSV representation of a list of items (without discussion).
   *
   * @param items list of associative arrays with fields given in the
   *              "database column" of `CONST::ITEM_DATA`.
   *
   * @returns item as CSV string.
   */
  public function asCSV()
  {
    $str = '';
    foreach ($this->items as $item) {
      $str .= '"' . implode('","', $this->idsToString($item)) . '"' . "\n";
    }
    return $str;
  }
}
?>
