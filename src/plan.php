<?php

if (!isset($AUTHID) || $AUTHID === false) {
        error_log("Not authorized.");
        http_response_code(401);
        exit(0);
}

$time_start = strtotime("today", time());
$time_end = time();

$today_prepared_query = pg_prepare(
        $db_conn,
        "receive_plan_today",
        "SELECT SUM(quantity) as quantity, to_char(to_timestamp(time)::timestamp, 'HH24:00') as date FROM history WHERE fk_token_id IN (SELECT id FROM token WHERE fk_name = (SELECT fk_name FROM token where id=$1) ) AND time >= $2 AND time <= $3 GROUP BY date ORDER BY date desc"
);

if ($today_prepared_query === false) {
        error_log(pg_last_error($db_conn));
        http_response_code(500);
        return;
}

$today_result_query = pg_execute(
        $db_conn,
        "receive_plan_today",
        [
                $AUTHID,
                $time_start,
                $time_end
        ]
);

if ($today_result_query === false) {
        error_log(pg_last_error($db_conn));
        http_response_code(500);
        return;
}

$today_result = [];
while (($row = pg_fetch_array($today_result_query, NULL, PGSQL_ASSOC)) !== false) {
        $today_result[] = $row;
}

$sum = 0;
foreach ($today_result as $value) {
        $sum += $value['quantity'];
}


$need_prepared_query = pg_prepare(
        $db_conn,
        "receive_plan_need",
        "SELECT daily_need - $1 FROM account WHERE name = (SELECT fk_name FROM token WHERE id = $2)"
);

if ($need_prepared_query === false) {
        error_log(pg_last_error($db_conn));
        http_response_code(500);
        return;
}

$need_result_query = pg_execute(
        $db_conn,
        "receive_plan_need",
        [
                $sum,
                $AUTHID
        ]
);

if ($need_result_query === false) {
        error_log(pg_last_error($db_conn));
        http_response_code(500);
        return;
}

$need_result = pg_fetch_result($need_result_query, 0);

$plan_result = [];
$shot_drink_ml = 400;

$num_drink = ceil($need_result / $shot_drink_ml);

if ($num_drink == 0) {
        return ["plan" => [], "need" => 0];
}

require("time.php");

$time_now = get_user_time($db_conn, $AUTHID);

$current_hour = date("H", $time_now);
$last_drink_hour = substr($today_result[0]['date'], 0, -3);
if ($last_drink_hour == $current_hour) {
        $starting_hour = $current_hour + 1;
} else {
        $starting_hour = $current_hour;
}

$remaining_hours = 23 - $starting_hour;

$drink_interval = ceil($remaining_hours / $num_drink);

for ($i = 0; $i < $num_drink; $i++) {
        $hour_planned = $starting_hour + ($drink_interval * ($i));
        if ($hour_planned < 24) {
                $plan_result[$i]['date'] = date("H:00", strtotime($hour_planned . ":00"));
                $plan_result[$i]['quantity'] = $shot_drink_ml;
        }
}

return ["plan" => $plan_result, "need" => $need_result];

?>