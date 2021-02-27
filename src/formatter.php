<?php

require_once("config.php");

class formatter
{
  private $items;
  private $columns;

  /* From https://savannah.gnu.org/css/internal/base.css */
  private $CSS_COLORS = [
    'open'   => ['#fff2f2', '#ffe8e8', '#ffe0e0', '#ffd8d8', '#ffcece',
                 '#ffc6c6', '#ffbfbf', '#ffb7b7', '#ffadad'],
    'closed' => ['#f5ffeb', '#edffe6', '#eeffe1', '#e0ffd5', '#ccffbb',
                 '#c6ffb9', '#c0ffb2', '#adffa4', '#a0ff9d']
  ];

  /**
   * Constructor.
   *
   * @param items array of associative array with fields given in the
   *              "database column" of `CONST::ITEM_DATA`.
   *
   * @param columns array of columns to display.
   */
  public function  __construct($items, $columns)
  {
    $this->items   = $items;
    $this->columns = $columns;
  }

  /**
   * Reduce columns of a given data row.
   *
   * Should be called last before the output.
   *
   * @param item associative array with fields given in the "database column"
   *             of `CONST::ITEM_DATA`.
   *
   * @returns item with only queried columns.
   */
  private function reduceColumns($item)
  {
    $new_item = array();
    foreach (array_merge(['refresh'], $this->columns) as $key) {
      if (array_key_exists($key, $item)) {
        $new_item[$key] = $item[$key];
      }
    }
    return $new_item;
  }

  /**
   * Translate IDs to human readable strings.
   *
   * @param item associative array with fields given in the "database column"
   *             of `CONST::ITEM_DATA`.
   *
   * @returns item with all IDs as human readable strings.
   */
  private function idsToString($item)
  {
    $item['TrackerID']  = CONFIG::TRACKER[$item['TrackerID']];
    $item['OpenClosed'] = CONFIG::ITEM_STATE[$item['OpenClosed']];
    return $item;
  }

  /**
   * Translate TIMESTAMPS to human readable strings.
   *
   * @param item associative array with fields given in the "database column"
   *             of `CONST::ITEM_DATA`.
   *
   * @returns item with all TIMESTAMPS as human readable strings.
   */
  private function timestampToString($item)
  {
    $toDays = function ($t) {
      $t = intval((time() - $t) / 60 / 60 / 24);
      if ($t > 365) {
        return '> ' . intval($t / 365) . ' year(s) ago';
      }
      return ($t === 0) ? '< a day' : "$t day(s) ago";
    };
    $item['SubmittedOn'] = $toDays($item['SubmittedOn']);
    $item['LastComment'] = $toDays($item['LastComment']);
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
    $id  = $item['ItemID'];
    $refreshLink = "<a onclick=\"apiUpdateItem(this, '"
                   . $item['TrackerID'] . "', '$id')\" href=\"#\">🔄</a>";
    $url = CONFIG::BASE_URL . '/' . $item['TrackerID'] . "/index.php?$id";
    $item['ItemID'] = "<a href=\"$url\">$id</a>";
    $item['Title']  = "<a href=\"$url\">" . $item['Title'] . "</a>";
    $item = array_merge(['refresh' => $refreshLink], $item);
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
    $str = '<tr>' . (($color) ? "<th$css>&nbsp;</th>" : '')
           . "<th$css>" . implode("</th><th$css>", $columns) . '</th></tr>';
    foreach ($this->items as $item) {
      $item = $this->idsToString($item);
      $item = $this->timestampToString($item);
      $css = ($color) ? $this->addCSS($item) : '';
      if ($color) {
        $item = $this->addURLs($item);
      }
      $item = $this->reduceColumns($item);
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
    $items = $this->items;  // work on a copy
    foreach ($items as &$item) {
      $item = $this->reduceColumns($this->idsToString($item));
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
    $str = '"' . implode('","', $this->columns) . '"' . "\n";
    foreach ($this->items as $item) {
      $item = $this->reduceColumns($this->idsToString($item));
      $str .= '"' . implode('","', $item) . '"' . "\n";
    }
    return $str;
  }
}
?>
