#Mysqli prepare statement class

License: MIT

This PHP class lat you perform Mysqli prepared statements in just one line.

Documenttation:

## Construct the object
The first and only parameter in the constructer is an array with host, username, password and database name for the mysql server
```php
$sqli = new sqli(["127.0.0.1", "", "", "test"]);
```
_Find more examples in Example.php_

## push, pull_once and pull_multiple metode
### Parameters
$query - standard sql statement with question marks as placeholders

$dataMode - string with the number of variables and length of string, types must match the parameters in the statement.

     * i for integer
     
     * d for double
     
     * s for string
     
     * b for blob and will be sent in packets
     
$parameters - array of variables for the placehoders

### Return
All the metohed retun a standart object with following parameters, and some more for the spefic methodes

status: true if the prepare method in the ordinery mysqli class return true

error_msg: error from the connection if the prepare methode returns false

affected_rows: affected rows from the sql statement

## push method
```php
$sqli->push("insert into test (msg) VALUES (?)", "s", "Just a test");
```
_Find more examples in Example.php_

Do also return following:

id: the id there has ben insert if sql statement was a insert statement

## pull_once method
```php
$sqli->pull_once("SELECT msg as message FROM test WHERE id=?", "i", 1);
```
_Find more examples in Example.php_

Do also return following:

data: array of the feilds of the (first) row there has ben selected, empty array of nothing has ben selected

count: the row count there has ben selected from the sql statement

## pull_multiple method
```php
$sqli->pull_multiple("SELECT id, msg as message FROM test");
```
_Find more examples in Example.php_

Do also return following:

data: array of the rows there has ben selected from the sql statement, each item is a array of the feilds

count: the row count there has ben selected from the sql statement