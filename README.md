# Release Burn Down Chart

Systematic overview about GNU Octave bugs at
<https://savannah.gnu.org/bugs/?group=octave>.

This is a helper web application to query the GNU Octave bug tracker at
GNU Savannah for particular bugs of interest, such as having a high severity,
etc.

One instance of this web application is hosted at

> <https://octave.space/savannah/>

## Deployment

Copy the files `savannah.php` and `index.html` to a web server supporting
1. execution of PHP scripts.
   There are currently no known issues about specific PHP versions.
2. creation of a JSON cache file `savannah.cache.json`
   in the same directory as `savannah.php`.

For reasons of efficiency, the data obtained from GNU Savannah is cached for
two minutes in the JSON cache file `savannah.cache.json` by default.
The caching can be configured at the top of `savannah.php`.

## Development

To add (or remove) particular queries, modify the `$queries` array in
`savannah.php`.
```php
$queries = array(
  array(
    true,  // count items for tracker sum (Bugs, Patches, ...)
    'Query label',
    'https://savannah.gnu.org/bugs/index.php?...'
  )
)
```
Try out a new query at GNU Savannah by tuning the "Display Criteria"
and finally copy the content of the browser address bar to the URL field.
