# SavannahAPI

Systematic overview about GNU Octave bugs at
<https://savannah.gnu.org/bugs/?group=octave>.

This is a helper web application to query the GNU Octave bug and patch tracker
at GNU Savannah for particular bugs of interest, such as having a high severity,
etc.

One instance of this web application is hosted at

> <https://octave.space/savannah/>

## Sophisticated API

As a byproduct of this effort the internal SQLite database conveniently via a
web API.  For example the following statement delivers the first 20 open bugs
in JSON format:

> <https://octave.space/savannah/api.php?Action=get&Format=JSON&OpenClosed=open&OrderBy=TrackerID,!ItemID&Limit=20>

## Development and deployment

> This project is work-in-progress!

This project is developed for the needs of the GNU Octave project, but can with
moderate effort be adapted for other GNU Savannah projects as well.

Despite a hopefully better interface, this project can also be used as data
exporter, if one wants to migrate away from GNU Savannah bug trackers, for
example.

To deploy this project on your web server, copy all files to a directory,
which permits:
1. execution of PHP scripts.
   There are currently no known issues about specific PHP versions.
2. creation of a SQLite cache file `savannah.cache.sqlite`
   in the web root directory.

The first run(s) of `api.php?Action=update` will take ages until all data
has been crawled to the local SQLite database.  Successive runs are
significantly faster and can be used to keep the database in sync with Savannah.
