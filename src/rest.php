<?php
header('Content-Type: application/json; charset=utf-8');


$AUTHID = require("auth.php");

if (!isset($AUTHID) || $AUTHID === false) {
        error_log("Not authorized.");
        http_response_code(401);
        exit(0);
}

date_default_timezone_set(LOCALE_TIMEZONE);

$response = [];

if ($_POST['type'] === "send") {
        $response = require("send.php");
} else if ($_POST['type'] === "receive") {
        $response = require("receive.php");
} else if ($_POST['type'] === "plan") {
        $response = require("plan.php");
} else {
        error_log("Requested type is invalid.");
        http_response_code(400);
}

echo json_encode($response);

?>