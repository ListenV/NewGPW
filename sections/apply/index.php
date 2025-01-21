<?
enforce_login();

$Role  = '';
$Title = '';
$Body  = '';
$Error = '';

$action = isset($_GET['action']) ? $_GET['action'] : '';
switch ($action) {
    case 'admin':
        if (check_perms('admin_manage_applicants')) {
            include(CONFIG['SERVER_ROOT'] . '/sections/apply/admin.php');
        } else {
            include(CONFIG['SERVER_ROOT'] . '/sections/apply/apply.php');
        }
        break;

    case 'view':
        if (check_perms('admin_manage_applicants') || (!check_perms('admin_manage_applicants') && Applicant::user_is_applicant($LoggedUser['ID']))) {
            include(CONFIG['SERVER_ROOT'] . '/sections/apply/view.php');
        } else {
            include(CONFIG['SERVER_ROOT'] . '/sections/apply/apply.php');
        }
        break;

    case 'save':
    default:
        include(CONFIG['SERVER_ROOT'] . '/sections/apply/apply.php');
        break;
}
