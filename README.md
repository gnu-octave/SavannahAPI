# SavannahAPI

A more systematic overview about bugs and patches

> <https://octave.space/savannah/>


## Usage

### Top search bar

Works as any other usual search bar:
enter keywords, hit enter, or press the left search button.
The right clear button clears all fields.

If the input are simple keywords all items (bugs and patches) Title and
Discussion text will be searched.

- **white space** separated words are treated as a single search term.
  For example, `"krylov subspace"` will look for titles and discussions
  containing both words in this order.  The search result is likely different
  from `"subspace krylov"`.

- **white space matters:** `"krylov subspace"` and `"krylov  subspace"`
  (with two spaces) are different search terms.

- **commas** are treated as "OR-searches".
  For example, `"krylov,subspace"` and `"subspace,krylov"` both have the same
  search result.  Titles and discussions containing either "krylov" or
  "subspace" or both terms will be found.

The top search bar also accepts API parameter inputs,
which are explained in the next section.


### Advanced API searches

To be explained...


### The API syntax and grammar

Explanation:
- `[string]`: string with white space, e.g. `"krylov  subspace"`
- `[strings]`: `[string],[string],...`, e.g. `"krylov,subspace"`
- `[int]`: non-negative integer, e.g. `1`, `42`, `12345`, ...
- `[ints]`: `[int],[int],...`, e.g. `42,12345`
- `{A|B|...}`: exactly one, e.g., `A`, `B`, ...
- `{A,B,...}`: combination, e.g., `A`, `B`, `A,B`, `B,A`, ...
- `[IGNORED]`: value ignored (reserved keyword)

```
Action=get

    Keywords=[strings]
    Title=[strings]
    Category=[strings]
    Severity=[strings]
    Priority=[strings]
    ItemGroup=[strings]
    Status=[strings]
    AssignedTo=[strings]
    Release=[strings]
    OperatingSystem=[strings]
    Limit=[int]
    ItemID=[ints]
    TrackerID={bugs,patch}
    OpenClosed={open,closed}
    Format={HTML|HTMLCSS|JSON|JSONFULL|CSV}
    Columns={TrackerID,ItemID,Title,SubmittedOn,LastComment,Category,
             Severity,Priority,ItemGroup,Status,AssignedTo,OpenClosed,
             Release,OperatingSystem,SubmittedBy,OriginatorName,
             UpdateCallback}
    LastComment=[IGNORED]
    SubmittedOn=[IGNORED]


Action=update

    TrackerID={bugs,patch}
    ItemID=[ints]
```


### Saving queries in the local web browser

To be explained...


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
