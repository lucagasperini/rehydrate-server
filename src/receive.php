<?php

if (!isset($AUTHID) || $AUTHID === false) {
        error_log("Not authorized.");
        http_response_code(401);
        exit(0);
}

if (isset($_POST['sum']) && !empty($_POST['sum'])) {
        $mode_sum = $_POST['sum'];
} else {
        $mode_sum = false;
}

if (isset($_POST['time_start']) && !empty($_POST['time_start'])) {
        if ($_POST['time_start'] === "today") {
                $time_start = strtotime("today", time());
        } else if ($_POST['time_start'] === "24h") {
                $time_start = strtotime("-1 day", time());
        } else if ($_POST['time_start'] === "week") {
                $time_start = strtotime("-7 day", time());
        } else if ($_POST['time_start'] === "month") {
                $time_start = strtotime("-1 month", time());
        } else if ($_POST['time_start'] === "year") {
                $time_start = strtotime("-1 year", time());
        } else if (is_numeric($_POST['time_start'])) {
                $time_start = $_POST['time_start'];
        } else {
                $time_start = 0;
        }
} else {
        $time_start = 0;
}

if (isset($_POST['time_end']) && !empty($_POST['time_end']) && is_numeric($_POST['time_end'])) {
        $time_end = $_POST['time_end'];
} else {
        $time_end = time();
}

function receive_sum($db_conn, $token_id, $time_start, $time_end, $query_label, $query)
{
        require("time.php");
        $timezone = get_user_timezone($db_conn, $token_id);
        
        $result_timezone_query = pg_query(
                $db_conn,
                "set time zone '".$timezone."';"
        );

        if ($result_timezone_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return;
        }
        
        $prepared_query = pg_prepare(
                $db_conn,
                $query_label,
                $query
        );

        if ($prepared_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return;
        }

        $result_query = pg_execute(
                $db_conn,
                $query_label,
                [
                        $token_id,
                        $time_start,
                        $time_end
                ]
        );

        if ($result_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return;
        }

        $result = [];
        while (($row = pg_fetch_array($result_query, NULL, PGSQL_ASSOC)) !== false) {
                $result[] = $row;
        }
        return $result;
}

if ($mode_sum === "hourly") {
        return receive_sum(
                $db_conn,
                $AUTHID,
                $time_start,
                $time_end,
                "receive_" . $mode_sum,
                "SELECT SUM(quantity) as quantity, to_char(to_timestamp(time)::timestamp, 'YYYY-MM-DD HH24:00') as date FROM history WHERE fk_token_id IN (SELECT id FROM token WHERE fk_name = (SELECT fk_name FROM token where id=$1) ) AND time >= $2 AND time <= $3 GROUP BY date ORDER BY date"
        );
} else if ($mode_sum === "daily") {
        return receive_sum(
                $db_conn,
                $AUTHID,
                $time_start,
                $time_end,
                "receive_" . $mode_sum,
                "SELECT SUM(quantity) as quantity, to_timestamp(time)::date as date FROM history WHERE fk_token_id IN (SELECT id FROM token WHERE fk_name = (SELECT fk_name FROM token where id=$1) ) AND time >= $2 AND time <= $3 GROUP BY date ORDER BY date"
        );
} else if ($mode_sum === "weekly") {
        return receive_sum(
                $db_conn,
                $AUTHID,
                $time_start,
                $time_end,
                "receive_" . $mode_sum,
                "SELECT SUM(quantity) as quantity, to_char(to_timestamp(time)::date, 'YYYY-IW') as date FROM history WHERE fk_token_id IN (SELECT id FROM token WHERE fk_name = (SELECT fk_name FROM token where id=$1) ) AND time >= $2 AND time <= $3 GROUP BY date ORDER BY date"
        );
} else if ($mode_sum === "monthly") {
        return receive_sum(
                $db_conn,
                $AUTHID,
                $time_start,
                $time_end,
                "receive_" . $mode_sum,
                "SELECT SUM(quantity) as quantity, to_char(to_timestamp(time)::date, 'YYYY-MM') as date FROM history WHERE fk_token_id IN (SELECT id FROM token WHERE fk_name = (SELECT fk_name FROM token where id=$1) ) AND time >= $2 AND time <= $3 GROUP BY date ORDER BY date"
        );
} else if ($mode_sum === "yearly") {
        return receive_sum(
                $db_conn,
                $AUTHID,
                $time_start,
                $time_end,
                "receive_" . $mode_sum,
                "SELECT SUM(quantity) as quantity, extract(year from to_timestamp(time)::date) as date FROM history WHERE fk_token_id IN (SELECT id FROM token WHERE fk_name = (SELECT fk_name FROM token where id=$1) ) AND time >= $2 AND time <= $3 GROUP BY date ORDER BY date"
        );
} else {
        return receive_sum(
                $db_conn,
                $AUTHID,
                $time_start,
                $time_end,
                "receive_nosum",
                "SELECT quantity as quantity, time FROM history WHERE fk_token_id IN (SELECT id FROM token WHERE fk_name = (SELECT fk_name FROM token where id=$1) ) AND time >= $2 AND time <= $3 ORDER BY time"
        );
}

?>