<?php

$Redirects = [
    'browse'  => 'torrents.php',
    'collage' => 'collages.php',
    'signup'  => 'register.php',
    'whitelist' => 'rules.php?p=clients',
    'forum' => 'forums.php',
    'randomcollage' => 'random.php?action=collage'
];

$PathInfo = pathinfo($_SERVER['SCRIPT_NAME']);
$Document = $PathInfo['filename'];
$SSL = $_SERVER['SERVER_PORT'] === '443';
if ($PathInfo['dirname'] !== '/') {
    header("Location: /index.php");
} elseif (isset($Redirects[$Document])) {
    $Seperator = (strpos($Redirects[$Document], "?") === false) ? "?" : "&";
    $Rest = (!empty($_SERVER['QUERY_STRING'])) ? $Seperator . $_SERVER['QUERY_STRING'] : "";
    header("Location: {$Redirects[$Document]}{$Rest}");
} elseif (in_array($Document, ['announce', 'scrape'])) {
    echo "d14:failure reason40:Invalid .torrent, try downloading again.e";
    die();
}

$Valid = false;
switch ($Document) {
    case 'tg':
    case 'peerupdate':
        /** @noinspection PhpMissingBreakStatementInspection */
    case 'schedule':
        define('MEMORY_EXCEPTION', true);
        define('TIME_EXCEPTION', true);
    case 'artist':
    case 'bookmarks':
    case 'collages':
    case 'comments':
    case 'forums':
    case 'friends':
    case 'torrents':
    case 'upload':
    case 'user':
    case 'userhistory':
        /** @noinspection PhpMissingBreakStatementInspection */
    case 'wiki':
        define('ERROR_EXCEPTION', true);
    case 'ajax':
    case 'apply':
    case 'blog':
    case 'bonus':
    case 'captcha':
    case 'chat':
    case 'contest':
    case 'donate':
    case 'enable':
    case 'error':
    case 'inbox':
    case 'index':
    case 'irc':
    case 'locked':
    case 'log':
    case 'login':
    case 'logout':
    case 'random':
    case 'referral':
    case 'register':
    case 'reports':
    case 'reportsv2':
    case 'requests':
    case 'rules':
    case 'signup':
    case 'sitehistory':
    case 'staff':
    case 'staffblog':
    case 'staffpm':
    case 'stats':
    case 'top10':
    case 'activity':
    case 'badges':
    case 'subtitles':
        $Valid = true;
        break;
}

if (!$Valid) {
    $_SERVER['SCRIPT_NAME'] = 'error.php';
    $_SERVER['SCRIPT_FILENAME'] = 'error.php';
    $Error = 404;
}
require_once(__DIR__ . '/../classes/script_start.php');
