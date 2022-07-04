<?php

require_once("config.php");

/**
 * Manage database access.
 */
class db
{
  private $pdo;  /// database connection

  private static $instance = null;  /// Singleton pattern.

  /**
   * Constructor.
   */
  private final function __construct()
  {
    if ($this->pdo == null) {
      // Open database.
      try {
        $this->pdo = new PDO('sqlite:' . dirname(__FILE__) . '/'
                                       . CONFIG::DB_FILE);
      } catch (PDOException $e) {
        die("Cannot open database file (write protected?). "
            . "Exception:\n\t". $e->getMessage());
      }

      // Check table structure.
      $items_cols = '';
      foreach (array_values(CONFIG::ITEM_DATA) as $col) {
        $items_cols .= $col[0] . " " . $col[1] . ",";
      }
      $discussions_cols = '';
      foreach (CONFIG::DISCUSSION_DATA as $col) {
        $discussions_cols .= $col[0] . " " . $col[1] . ",";
      }
      $commands = ['CREATE TABLE IF NOT EXISTS Items (
                      ID      INTEGER PRIMARY KEY AUTOINCREMENT,
                      '. $items_cols. '
                      LastUpdated  TIMESTAMP NOT NULL)',
                   'CREATE TABLE IF NOT EXISTS Discussions (
                      ID      INTEGER PRIMARY KEY AUTOINCREMENT,
                      ItemID  INTEGER,
                      '. $discussions_cols. '
                      FOREIGN KEY (ItemID)
                        REFERENCES Items(ID)
                          ON UPDATE CASCADE
                          ON DELETE CASCADE
                    )',
                   'CREATE TABLE IF NOT EXISTS Timer (
                      ID      INTEGER PRIMARY KEY AUTOINCREMENT,
                      Time    TIMESTAMP NOT NULL
                    )'];
      try {
        foreach ($commands as $command) {
          $this->pdo->exec($command);
        }
      } catch (PDOException $e) {
        exit("Database tables could not be created. "
             . "Exception:\n\t". $e->getMessage());
      }

      // Check timers are available.
      $lastTimerName = CONFIG::TIMER[count(CONFIG::TIMER) - 1];
      while ($this->getTimer($lastTimerName) === false) {
        $this->pdo->exec('INSERT INTO Timer (Time) VALUES (0)');
      }
    }
  }


  /**
   * Get database instance.
   *
   * @returns database instance.
   */
  public function getInstance()
  {
    if (!isset(self::$instance)) {
      self::$instance = new db();
    }
    return self::$instance;
  }


  /**
   * Retrieve a filtered list of items.
   *
   * @param filter a validated array like created from `$_GET`.
   *
   * @returns an array of items.
   */
  public function getItems($filter = null)
  {
    $data = array();  // return value
    // SQL command
    $sqlSELECT  = array_column(array_values(CONFIG::ITEM_DATA), 0);
    $sqlWHERE   = array();
    $sqlLIMIT   = '';
    $sqlORDERBY = ['TrackerID ASC', 'ItemID DESC'];
    $sqlDATA    = array();
    if ($filter !== null) {
      foreach ($filter as $key => $value) {
        $op = null;
        $makeValid = null;
        $eq   = '=';
        $like = 'LIKE';
        $conj = 'OR';
        if (substr($key, -1) === '!') {  // inverted query
          $key  = substr($key, 0, -1);
          $eq   = '<>';
          $like = 'NOT LIKE';
          $conj = 'AND';
        }
        switch ($key) {
          case 'TrackerID':
            $op = (is_null($op) ? $eq : $op);
            $makeValid = (!is_null($makeValid) ? $makeValid
              : function($v){ return array_search($v, CONFIG::TRACKER); });
          case 'OpenClosed':
            $op = (is_null($op) ? $eq : $op);
            $makeValid = (!is_null($makeValid) ? $makeValid
              : function($v){ return array_search($v, CONFIG::ITEM_STATE); });
          case 'AttachedFiles':
            $op = (is_null($op) ? $eq : $op);
            $makeValid = (!is_null($makeValid) ? $makeValid
              : function($v){ return "$v"; });
          case 'ItemID':
          case 'Title':
          case 'Submitter':
          //TODO: useful date queries
          //case 'Submitted':
          //case 'LastComment':
          case 'Category':
          case 'Severity':
          case 'Priority':
          case 'ItemGroup':
          case 'Status':
          case 'AssignedTo':
          case 'OriginatorName':
          case 'Release':
          case 'OperatingSystem':
          case 'AttachedFileNames':
            $op = (is_null($op) ? $like : $op);
            $makeValid = (!is_null($makeValid) ? $makeValid
              : function($v){ return "%$v%"; });
            $sql = array();
            foreach ($value as $k => $v) {
              array_push($sql, "$key $op :$key$k");
              $sqlDATA = array_merge($sqlDATA, [":$key$k" => $makeValid($v)]);
            }
            array_push($sqlWHERE, '(' . implode(" $conj ",  $sql) . ')');
            break;
          case 'Keywords':
            $makeValid = function($v){ return "%$v%"; };
            $sql         = array();
            $sqlKeywords = array();
            foreach ($value as $k => $v) {
              array_push($sql,         "Title LIKE :$key$k");
              array_push($sqlKeywords, "Text  LIKE :$key$k");
              $sqlDATA = array_merge($sqlDATA, [":$key$k" => $makeValid($v)]);
            }
            $sqlKeywords =
              'ID IN (SELECT DISTINCT(ItemID)
                      FROM   Discussions
                      WHERE  (' . implode(" OR ",  $sqlKeywords) . '))';
            array_push($sql, $sqlKeywords);
            array_push($sqlWHERE, '(' . implode(" OR ",  $sql) . ')');
            break;
          case 'Limit':
            $sqlLIMIT = ' LIMIT :Limit ';
            $sqlDATA = array_merge($sqlDATA, [':Limit' => (int) $value]);
            break;
          case 'OrderBy':
            $sqlORDERBY = array();
            foreach ($value as $v) {
              array_push($sqlORDERBY, ($v[0] === '!') ? substr($v, 1) . ' DESC'
                                                      : "$v ASC");
            }
            break;
        }
      }
    }
    // Trivial condition, if no conditions are given.
    if (count($sqlWHERE) == 0) {
      array_push($sqlWHERE, '1=1');
    }
    $command = 'SELECT ' . implode(",",  $sqlSELECT) . '
                FROM Items
                WHERE '
                . implode(" AND ", $sqlWHERE) . '
                ORDER BY '
                . implode(", ", $sqlORDERBY)
                . $sqlLIMIT;
    $stmt = $this->pdo->prepare($command);
    if ($stmt === false) {
      DEBUG_LOG("SQL command preparation failed: $command");
      return $data;
    }
    try {
      $stmt->execute($sqlDATA);
    } catch (Exception $e) {
      var_dump($e);
      return $data;
    }
    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
      array_push($data, $item);
    }
    return $data;
  }


  /**
   * Retrieve all discussions to a single item.
   *
   * @param trackerID see index value of `CONST::TRACKER`.
   *
   * @param itemID non-negative integer.
   *
   * @returns an array of discussions.
   */
  public function getDiscussion($trackerID, $itemID) {
    $data = array();  // return value

    $command = 'SELECT ID
                FROM   Items
                WHERE TrackerID=:TrackerID
                  AND    ItemID=:ItemID';
    $stmt = $this->pdo->prepare($command);
    $stmt->execute([':TrackerID' => $trackerID,
                    ':ItemID'    => $itemID]);
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($id === false) {
      return $data;
    }
    $command = 'SELECT   Date, Author, Text
                FROM     Discussions
                WHERE    ItemID=:ItemID
                ORDER BY Date ASC';
    $stmt = $this->pdo->prepare($command);
    $stmt->execute([':ItemID' => $id['ID']]);
    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
      array_push($data, $item);
    }
    return $data;
  }


  /**
   * Get maximal item ID from a tracker.
   *
   * @param trackerID see index value of `CONST::TRACKER`.
   *
   * @returns item ID as integer or `false` on error.
   */
  public function getMaxItemIDFromTracker(int $trackerID)
  {
    $command = 'SELECT MAX(ItemID) AS ItemID
                FROM  Items
                WHERE TrackerID=:TrackerID';
    $stmt = $this->pdo->prepare($command);
    $stmt->execute([':TrackerID' => $trackerID]);
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($id === false) ? false : (int) $id["ItemID"];
  }



  /**
   * Get newest LastComment timestamp from a tracker.
   *
   * @param trackerID see index value of `CONST::TRACKER`.
   *
   * @returns timestamp as integer or `false` on error.
   */
  public function getMaxLastCommentFromTracker(int $trackerID)
  {
    $command = 'SELECT MAX(LastComment) AS ItemID
                FROM  Items
                WHERE TrackerID=:TrackerID';
    $stmt = $this->pdo->prepare($command);
    $stmt->execute([':TrackerID' => $trackerID]);
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($id === false) ? false : (int) $id["ItemID"];
  }


  /**
   * Get timer value.
   *
   * @param timerName see `CONST::TIMER`.
   *
   * @returns timestamp as integer or `false` on error.
   */
  public function getTimer($timerName)
  {
    $id = array_search($timerName, CONFIG::TIMER);
    if ($id === false) {
      return false;
    }
    $command = 'SELECT Time FROM Timer WHERE ID = :ID';
    $stmt = $this->pdo->prepare($command);
    $stmt->execute([':ID' => $id + 1]);  // Index shift for database!
    $timestamp = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($timestamp !== false) ? (int) $timestamp["Time"] : false;
  }


  /**
   * Set timer value.
   *
   * @param timerName see `CONST::TIMER`.
   * @param timestamp value to set, for example `time()`.
   *
   * @returns nothing `null` or `false` on error.
   */
  public function setTimer($timerName, int $timestamp)
  {
    $id = array_search($timerName, CONFIG::TIMER);
    if ($id === false) {
      return false;
    }
    $command = 'UPDATE Timer
                SET    Time = :Time
                WHERE  ID   = :ID';
    $stmt = $this->pdo->prepare($command);
    $stmt->execute([':Time' => $timestamp,
                    ':ID'   => $id + 1]);  // Index shift for database!
  }


  /**
   * Update an item with discussion.
   *
   * @param item associative array with fields given in the "database column"
   *             of `CONST::ITEM_DATA`.
   * @param discussion associative array with fields given in the "database
   *                   column" of `CONST::DISCUSSION_DATA`.
   */
  public function update($item, $discussion)
  {
    $id = $this->getInteralItemID($item['ItemID'], $item['TrackerID']);
    if ($id > 0) {
      $this->updateItems($id, $item);
    } else {
      $id = $this->insertIntoItems($item);
    }
    foreach ($discussion as $comment) {
      $cid = $this->getCommentID($id, $comment['Date']);
      if ($cid === -1) {
        DEBUG_LOG("----> New comment added.");
        $this->insertIntoDiscussions($id, $comment);
      }
    }
  }


  private function insertIntoItems($item)
  {
    $columns = array_column(array_values(CONFIG::ITEM_DATA), 0);
    $command = 'INSERT INTO Items
                          ( ' . implode(",",  $columns) . ',LastUpdated)
                    VALUES(:' . implode(",:", $columns) . ',:now)';
    $db = $this->pdo;
    $stmt = $db->prepare($command);
    $cols[':now'] = time();
    foreach ($columns as $c) {
      $cols[':' . $c] = $item[$c];
    }
    $stmt->execute($cols);
    return $db->lastInsertId();
  }


  private function insertIntoDiscussions($itemID, $comment)
  {
    $columns = array_column(CONFIG::DISCUSSION_DATA, 0);
    $command = 'INSERT INTO Discussions
                          ( ItemID, ' . implode(",",  $columns) . ')
                    VALUES(:itemID,:' . implode(",:", $columns) . ')';
    $stmt = $this->pdo->prepare($command);
    $cols[':itemID'] = $itemID;
    foreach ($columns as $c) {
      $cols[':' . $c] = $comment[$c];
    }
    $stmt->execute($cols);
  }


  private function updateItems(int $id, $item)
  {
    $columns = array_column(array_values(CONFIG::ITEM_DATA), 0);
    $command = 'UPDATE Items SET ';
    foreach ($columns as $c) {
      $command .= "$c = :$c, ";
    }
    $command   .= 'LastUpdated = :now
                   WHERE ID=:ID';
    $stmt = $this->pdo->prepare($command);
    $cols[':ID'] = $id;
    $cols[':now'] = time();
    foreach ($columns as $c) {
      $cols[':' . $c] = $item[$c];
    }
    $stmt->execute($cols);
  }


  private function getCommentID(int $itemID, int $date)
  {
    $command = 'SELECT ID FROM Discussions WHERE ItemID=:ItemID
                                             AND Date=:Date';
    $stmt = $this->pdo->prepare($command);
    $stmt->execute([
      ':ItemID' => $itemID,
      ':Date'   => $date
      ]);
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($id === false) ? -1 : (int) $id["ID"];
  }


  private function getInteralItemID(int $itemID, int $trackerID)
  {
    $command = 'SELECT ID FROM Items WHERE ItemID=:ItemID
                                       AND TrackerID=:TrackerID';
    $stmt = $this->pdo->prepare($command);
    $stmt->execute([
      ':ItemID'    => $itemID,
      ':TrackerID' => $trackerID
      ]);
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($id === false) ? -1 : (int) $id["ID"];
  }

}

?>
