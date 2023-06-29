<?

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

function get_user_time($db_conn, $userid)
{
        $timezone = get_user_timezone($db_conn, $userid);
        return (new DateTime('now', new DateTimeZone($timezone)))->getTimestamp();
}

?>