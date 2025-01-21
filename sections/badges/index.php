<?
if (!CONFIG['ENABLE_BADGE']) {
    error(404);
}
enforce_login();
if (empty($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}
switch ($_REQUEST['action']) {
    case 'display':
        include CONFIG['SERVER_ROOT'] . '/sections/badges/display.php';
        break;
    case 'history':
        include CONFIG['SERVER_ROOT'] . '/sections/badges/history.php';
        break;
    case 'store':
        include CONFIG['SERVER_ROOT'] . '/sections/badges/store.php';
        break;
    default:
        include CONFIG['SERVER_ROOT'] . '/sections/badges/badge.php';
}
