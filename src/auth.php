<?php

require("config.php");

$db_conn = pg_connect(
        "host=" . DB_HOST .
        " port=" . DB_PORT .
        " dbname=" . DB_NAME .
        " user=" . DB_USER .
        " password=" . DB_PASS
);

if ($db_conn === false) {
        error_log("Cannot access database");
        http_response_code(500);
        return false;
}


if (isset($_POST['u']) && isset($_POST['p'])) {
        $login_prepared_query = pg_prepare(
                $db_conn,
                "login",
                "SELECT 1 FROM account WHERE name = $1 AND pass = $2"
        );

        if ($login_prepared_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return false;
        }

        $pass = hash("sha256", $_POST['p']);

        $login_result_query = pg_execute(
                $db_conn,
                "login",
                [$_POST['u'], $pass]
        );

        if ($login_result_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return false;
        }

        $login_result = pg_fetch_result($login_result_query, 0);

        if ($login_result !== "1") {
                error_log("Login failed");
                http_response_code(401);
                return false;
        }

        $token_prepared_query = pg_prepare(
                $db_conn,
                "token",
                "INSERT INTO token (token, fk_name, expire) VALUES ($1,$2,$3)"
        );

        if ($token_prepared_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return false;
        }


        // double length 1 byte = 2 hex, so $token is length 512
        $token = bin2hex(random_bytes(AUTH_TOKEN_LENGTH_BYTES));
        $expire = strtotime("+1 year", time());

        $token_result_query = pg_execute(
                $db_conn,
                "token",
                [
                        $token,
                        $_POST['u'],
                        $expire
                ]
        );

        if ($token_result_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return false;
        }

        echo $token;

        exit(0);
}

if (isset($_POST["token"])) {

        $verify_prepared_query = pg_prepare(
                $db_conn,
                "verify",
                "SELECT id FROM token WHERE token = $1 AND expire > $2"
        );

        if ($verify_prepared_query === false) {
                error_log(pg_last_error($db_conn));
                http_response_code(500);
                return false;
        }

        $verify_result_query = pg_execute(
                $db_conn,
                "verify",
                [$_POST["token"], time()]
        );

        $login_result = pg_fetch_result($verify_result_query, 0);

        if ($login_result === false) {
                error_log("Verification failed");
                http_response_code(401);
                return false;
        }

        return $login_result;
}

return false;

?>