<?php

if (!isset($AUTHID) || $AUTHID === false) {
        error_log("Not authorized.");
        http_response_code(401);
        exit(0);
}

?>