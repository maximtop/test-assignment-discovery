<?php

require_once __DIR__ . "/../vendor/autoload.php";

error_reporting(E_ALL);

use function \BalanceUpdater\counter;
use function \BalanceUpdater\makeTable;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
            integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <h1 class="text-center">Clients balance calculator</h1>
    <hr>
    <h4>Upload file with transactions and clients initial balance</h4>
    <form class="form-inline" enctype="multipart/form-data" action="index.php" method="POST">
        <div class="form-group">
            <input name="userfile" type="file"/>
            <input type="submit" value="Send File"/>
        </div>
    </form>
    <?php
    if ($_FILES["userfile"]["tmp_name"]) {
        try {
            if (\BalanceUpdater\isExtXlsx($_FILES["userfile"]['name'])) {
                $clientsUpdatedBalance = counter($_FILES["userfile"]["tmp_name"]);
                echo "<hr><h2>Results</h2>";
                echo makeTable($clientsUpdatedBalance);
            }
        } catch (Exception $e) {
            echo "<hr><h2>Script found an error: </h2>";
            echo $e->getMessage();
        }
    }
    ?>
</div>
</body>
</html>