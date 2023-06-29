<?php

if (!isset($AUTHID) || $AUTHID === false) {
        error_log("Not authorized.");
        http_response_code(401);
        exit(0);
}

include_once("time.php");

if (isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {

        $send_prepared_query = pg_prepare(
                $db_conn,
                "send",
                "INSERT INTO history (fk_token_id, quantity, time) VALUES ($1,$2,$3)"
        );

        if ($send_prepared_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return;
        }

        if (isset($_POST['time']) && is_numeric($_POST['time'])) {
                $time = $_POST['time'];
        } else {
                $time = get_user_time($db_conn, $AUTHID);
        }
        $send_result_query = pg_execute(
                $db_conn,
                "send",
                [
                        $AUTHID,
                        $_POST['quantity'],
                        $time
                ]
        );

        if ($send_result_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return;
        }

        return;
}

?>