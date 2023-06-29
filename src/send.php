<?php

if (!isset($AUTHID) || $AUTHID === false) {
        error_log("Not authorized.");
        http_response_code(401);
        exit(0);
}

function get_user_timezone($db_conn, $userid)
{
        $prepared_query = pg_prepare(
                $db_conn,
                'user_timezone',
                'SELECT timezone FROM account WHERE name=(SELECT fk_name FROM token WHERE id=$1)'
        );

        if ($prepared_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return null;
        }

        $result_query = pg_execute(
                $db_conn,
                'user_timezone',
                [
                        $userid,
                ]
        );

        if ($result_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return null;
        }

        //TODO: check timezone?
        $timezone = pg_fetch_result($result_query, 0);
        return $timezone;
}


if (isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
        $timezone = get_user_timezone($db_conn, $AUTHID);

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
                $time = (new DateTime('now', new DateTimeZone($timezone)))->getTimestamp();
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