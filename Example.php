<pre>
<?php

include "sqli.php";

// Connect to the database
$sqli = new sqli(["127.0.0.1", "", "", "test"]);


// Insert three rows to have some data to play with
$insert1 = $sqli->push("insert into test (msg) VALUES (?)", "s", "Just a test");
echo "Insert id:" . $insert1->id."\n";

$insert2 = $sqli->push("insert into test (msg) VALUES (?)", "s", "Just a test");
echo "Insert id:" . $insert2->id."\n";

$insert3 = $sqli->push("insert into test (msg) VALUES (?)", "s", "Just a test");
echo "Insert id:" . $insert3->id."\n\n";



// Show the data form the array
foreach ($sqli->pull_multiple("SELECT id, msg as message FROM test")->data as $row) {
    echo $row['id']." : ".$row['message']."\n";
}
echo "\n";


// Show one row
echo "First insert row: ".$sqli->pull_once("SELECT msg as message FROM test WHERE id=?", "i", $insert1->id)->data['message']."\n";

// Clere the table
echo "Deleted rows:".$sqli->push("DELETE FROM test")->affected_rows."\n";

// Some rows left?
echo "Rows left: ".$sqli->pull_multiple("SELECT * FROM test")->count;


/* HERES MY OUTPUT
Insert id:1
Insert id:2
Insert id:3

1 : Just a test
2 : Just a test
3 : Just a test

First insert row: Just a test
Deleted rows:3
Rows left: 0
 */
?>
</pre>
