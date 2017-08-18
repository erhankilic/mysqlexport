<?php
include 'Export.php';
$export = new MysqlExport\Export('backup', 'localhost', 'root', 'dev1');
$export->getDatabases();
$export->export();