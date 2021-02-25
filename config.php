<?php

class CONFIG
{
  /**
   * Configurable constant parameters crawler.
   */
  const BASE_URL    = 'https://savannah.gnu.org';
  const GROUP       = ['name' => 'GNU Octave',
                       'id'   => 'octave'];
  const CHUNK_SIZE  = 150;  // Items read from the overview page at once.
                            // (150 is Savannah maximum).
  const DELAY       = ['crawlItem'         =>      5,   // Seconds delay
                       'crawlNewItems'     => 1 * 60,   // Seconds delay
                       'crawlUpdatedItems' => 1 * 60];  // Seconds delay
  const MAX_CRAWL_ITEMS = 5;  // Maximal number of items updates per request.
                              // Automatic item updates are not affected
                              // by this setting.

  /**
   * There are seemingly no standard mail archives for Savannah.
   * Specify for each CONFIG::TRACKER below a mail archive url.
   */
  const TRACKER_MAIL_ARCHIVE = [
    'bugs'  => 'https://lists.gnu.org/archive/html/octave-bug-tracker/',
    'patch' => 'https://lists.gnu.org/archive/html/octave-patch-tracker/'
    ];

  /**
   * Configurable constant parameters database.
   */
  const DB_FILE = 'savannah.cache.sqlite';

  /**
   * Common data structures for the database and crawler (interface).
   *
   * Alter with care!  "ID" is a reserved database column name.
   */
  const ITEM_DATA = [
  // label on website       database column  , database datatype
    'TrackerID:'        => ['TrackerID'      , 'INTEGER NOT NULL'  ],
    'ID:'               => ['ItemID'         , 'INTEGER NOT NULL'  ],
    'Title:'            => ['Title'          , 'TEXT'              ],
    'Submitted by:'     => ['SubmittedBy'    , 'TEXT'              ],
    'Submitted on:'     => ['SubmittedOn'    , 'TIMESTAMP NOT NULL'],
    'Last comment:'     => ['LastComment'    , 'TIMESTAMP NOT NULL'],
    'Category:'         => ['Category'       , 'TEXT'              ],
    'Severity:'         => ['Severity'       , 'TEXT'              ],
    'Priority:'         => ['Priority'       , 'TEXT'              ],
    'Item Group:'       => ['ItemGroup'      , 'TEXT'              ],
    'Status:'           => ['Status'         , 'TEXT'              ],
    'Assigned to:'      => ['AssignedTo'     , 'TEXT'              ],
    'Originator Name:'  => ['OriginatorName' , 'TEXT'              ],
    'Open/Closed:'      => ['OpenClosed'     , 'INTEGER NOT NULL'  ],
    'Release:'          => ['Release'        , 'TEXT'              ],
    'Operating System:' => ['OperatingSystem', 'TEXT'              ]
    ];

  const DISCUSSION_DATA = [
  // database column, database datatype
    ['Date'         , 'TIMESTAMP NOT NULL'],
    ['Author'       , 'TEXT'              ],
    ['Text'         , 'LONGTEXT'          ]
    ];

  /// Timers hold in the database.
  const TIMER = ['crawlItem',
                 'crawlNewItems_bugs',
                 'crawlNewItems_patch',
                 'crawlUpdatedItems_bugs',
                 'crawlUpdatedItems_patch'];

  /// Currently supported Savannah trackers as IDs to not waste space
  /// in the database.
  const TRACKER = ['bugs', 'patch'];

  /// Currently supported Savannah item states as IDs to not waste space
  /// in the database.
  const ITEM_STATE = ['closed', 'open'];
}

function DEBUG_LOG($str)
{
  /* Uncomment for debugging. */
  echo("$str<br>");
  ob_flush();
  flush();
}

?>
