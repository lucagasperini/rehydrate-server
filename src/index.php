<!DOCTYPE html>

<?php
require("locale.php");
?>

<html>

<head>
        <title>ReHydrate</title>
        <script src="chart-js/chart.min.js"></script>
        <script src="webapp.js"></script>
        <link rel="stylesheet" href="webapp.css">
</head>


<?php
if (!isset($_COOKIE['token'])) {
        require("form_login.php");
} else {
        require("form_home.php");
}
?>

</html>