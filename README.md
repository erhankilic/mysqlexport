# MysqlExport
**Description:** Export the databases with php.


### Why I created this class?
I have a vps and I tired of manual backup. I decided to backup the databases automatically with cronjob then I created this class.


If you see an error or optimization please don't hesitate.
### TODO
Class isn't suitable for tables which has foreign keys. I'll update the class.

### Test Usage

```php
<?php
include 'Export.php';
$export = new MysqlExport\Export('backup', 'localhost', 'root', 'dev1');
$export->getDatabases();
$export->export();
```
### License
mysqlexport is an open source project by [Erhan Kılıç](http://erhankilic.org) that is licensed under [MIT](http://opensource.org/licenses/MIT).

### Contribution
Contribution are always **welcome and recommended**! Here is how:

- Fork the repository ([here is the guide](https://help.github.com/articles/fork-a-repo/)).
- Clone to your machine ```git clone https://github.com/YOUR_USERNAME/mysqlexport.git```
- Make your changes
- Create a pull request
