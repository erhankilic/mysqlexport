# MysqlExport
**Description:** Export the databases with php.


### Why I created this class?
I have a vps and I tired of manual backup. I decided to backup the databases automatically with cronjob then I created this class.


If you see an error or optimization please don't hesitate.
### TODO
Class isn't suitable for tables which has foreign keys. I'll update the class.

### Test Usage

```sh
<?php
include 'Export.php';
$export = new MysqlExport\Export('backup', 'localhost', 'root', 'dev1');
$export->getDatabases();
$export->export();
```
