<?php
require("config.php");

if (!isset($_GET['lang']))
        $lang = LOCALE_DEFAULT_LANG;
else
        $lang = $_GET['lang'] . ".utf8";

$locale_lang = $lang . LOCALE_DEFAULT_ENCODING;
$loaded_locale_lang = setlocale(LC_ALL, $locale_lang);
if ($locale_lang !== $loaded_locale_lang) {
        echo ("ERROR: Can't load locale language.\n");
        echo ($loaded_locale_lang);
        exit(1);
}

$loaded_locale_dir = bindtextdomain(LOCALE_DOMAIN, LOCALE_PATH);
if (LOCALE_PATH !== $loaded_locale_dir) {
        echo ("ERROR: Can't load locale directory.\n");
        echo ($loaded_locale_dir);
        exit(1);
}


$loaded_textdomain = textdomain(LOCALE_DOMAIN);
if (LOCALE_DOMAIN !== $loaded_textdomain) {
        echo ("ERROR: Can't load locale domain.\n");
        echo ($loaded_textdomain);
        exit(1);
}

?>