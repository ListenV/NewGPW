<?php
authorize();

if (empty($_POST['toid'])) {
    error(404);
}

if (!empty($LoggedUser['DisablePM']) && !isset($StaffIDs[$_POST['toid']])) {
    error(403);
}

if (isset($_POST['convid']) && is_number($_POST['convid'])) {
    $ConvID = $_POST['convid'];
    $Subject = '';
    $ToID = explode(',', $_POST['toid']);
    foreach ($ToID as $TID) {
        if (!is_number($TID)) {
            $Err = t('server.inbox.a_recipient_does_not_exist');
        }
    }
    $DB->query("
		SELECT UserID
		FROM pm_conversations_users
		WHERE UserID = '$LoggedUser[ID]'
			AND ConvID = '$ConvID'");
    if (!$DB->has_results()) {
        error(403);
    }
} else {
    $ConvID = '';
    if (!is_number($_POST['toid'])) {
        $Err = t('server.inbox.this_recipient_does_not_exist');
    } else {
        $ToID = $_POST['toid'];
    }
    $Subject = trim($_POST['subject']);
    if (empty($Subject)) {
        $Err = t('server.inbox.cannot_send_msg_without_subject');
    }
}
$Body = trim($_POST['body']);
if ($Body === '' || $Body === false) {
    $Err = t('server.inbox.cannot_send_msg_without_body');
}

if (!empty($Err)) {
    error($Err);
    //header('Location: inbox.php?action=compose&to='.$_POST['toid']);
    $ToID = $_POST['toid'];
    $Return = true;
    include(CONFIG['SERVER_ROOT'] . '/sections/inbox/compose.php');
    die();
}

$ConvID = Misc::send_pm($ToID, $LoggedUser['ID'], $Subject, $Body, $ConvID);


header('Location: ' . Inbox::get_inbox_link());
