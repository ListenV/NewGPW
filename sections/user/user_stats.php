<?php
if (isset($_GET['userid']) && check_perms('users_mod')) {
    if (!is_number($_GET['userid'])) {
        error(403);
    }

    $UserID = $_GET['userid'];
} else {
    $UserID = $LoggedUser['ID'];
}
$OwnProfile = $UserID == $LoggedUser['ID'];

$Charts = $Cache->get_value("user_statgraphs_$UserID");

if ($Charts == false) {
    $Charts = [
        ['Name' => 'Daily', 'Interval' => 1, 'Count' => 24],
        ['Name' => 'Monthly', 'Interval' => 24, 'Count' => 30],
        ['Name' => 'Yearly', 'Interval' => 24 * 7, 'Count' => 52]
    ];
    $Stats = ['Uploaded', 'Downloaded', 'BonusPoints', 'Torrents'];
    $Query = "
        SELECT Time, Uploaded, Downloaded, BonusPoints, Torrents
        FROM users_stats_%s
        WHERE UserID = ?
        ORDER BY Time DESC
        LIMIT %d";

    foreach ($Charts as &$Chart) {
        $Chart['name'] = strtolower($Chart['Name']);
        $DB->prepared_query(sprintf($Query, $Chart['name'], $Chart['Count']), $UserID);
        $Chart['Stats'] = $DB->has_results() ? array_reverse($DB->to_array(false, MYSQLI_ASSOC)) : [];
        $Chart['Start'] = count($Chart['Stats']) > 0 ? $Chart['Stats'][0]['Time'] : NULL;

        foreach ($Stats as $Stat) {
            $Chart[$Stat] = array_map(function ($a) use ($Stat) {
                return sprintf("[%d, %s]", strtotime($a['Time']) * 1000, $a[$Stat]);
            }, $Chart['Stats']);
        }
        $Chart['Buffer'] = array_map(function ($a) {
            return sprintf("[%d, %s]", strtotime($a['Time']) * 1000, $a['Uploaded'] - $a['Downloaded']);
        }, $Chart['Stats']);
        unset($Chart['Stats']);
        unset($Chart);
    }
    $Cache->cache_value("user_statgraphs_$UserID", $Charts, 3600);
}

View::show_header('User Stats', '', 'PageUserStat');
?>

<script src="<?= CONFIG['STATIC_SERVER'] ?>/functions/highcharts.js"></script>
<script src="<?= CONFIG['STATIC_SERVER'] ?>/functions/highcharts_custom.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php
        foreach ($Charts as $Chart) {
            if (count($Chart['Downloaded']) > 1) {
        ?>
                initialiseChart('<?= $Chart['name'] ?>-stats', 'Stats', [{
                    name: 'Downloaded',
                    data: [<?= implode(',', $Chart['Downloaded']) ?>]
                }, {
                    name: 'Uploaded',
                    data: [<?= implode(',', $Chart['Uploaded']) ?>]
                }, {
                    name: 'Buffer',
                    data: [<?= implode(',', $Chart['Buffer']) ?>]
                }], {
                    bytes: true
                });

                initialiseChart('<?= $Chart['name'] ?>-bp', 'Bonus Points', [{
                    name: 'Bonus Points',
                    data: [<?= implode(',', $Chart['BonusPoints']) ?>]
                }]);

                initialiseChart('<?= $Chart['name'] ?>-upload', 'Uploads', [{
                    name: 'Torrents',
                    data: [<?= implode(',', $Chart['Torrents']) ?>]
                }]);
        <?php }
        } ?>
    });
</script>

<div class="box">
    <div class="BodyHeader">
        <h2 class="BodyHeader-nav"><?= Users::format_username($UserID, true, true, true, false, true) ?></h2>
    </div>
    <div class="BodyNavLinks">
        <?php
        if (!$OwnProfile) {
        ?>
            <a href="inbox.php?action=compose&amp;toid=<?= $UserID ?>" class="brackets"><?= t('server.inbox.send_message') ?></a>
            <a href="reports.php?action=report&amp;type=user&amp;id=<?= $UserID ?>" class="brackets">Report user</a>
        <?php
        }
        if (check_perms('admin_reports')) {
        ?>
            <a href="reportsv2.php?view=reporter&amp;id=<?= $UserID ?>" class="brackets">Reports</a>
        <?php
        }
        if (check_perms('users_mod')) {
        ?>
            <a href="userhistory.php?action=token_history&amp;userid=<?= $UserID ?>" class="brackets">FL tokens</a>
        <?php
        }
        if (check_perms('users_mod') || ($LoggedUser['ID'] == $UserID && check_perms('site_user_stats'))) {
        ?>
            <a href="user.php?action=stats&amp;userid=<?= $UserID ?>" class="brackets">Stats</a>
        <?php
        }
        ?>
    </div>
</div>

<?php
foreach ($Charts as $Chart) { ?>
    <div class="box">
        <div class="head">
            <?= $Chart['Name'] ?> Stats
        </div>
        <div class="pad">
            <?php if (count($Chart['Downloaded']) > 1) { ?>
                <div id="<?= $Chart['name'] ?>-stats" style="width: 100%; height: 400px"></div>
                <br />
                <div id="<?= $Chart['name'] ?>-bp" style="width: 100%; height: 400px"></div>
                <br />
                <div id="<?= $Chart['name'] ?>-upload" style="width: 100%; height: 400px"></div>
            <?php } else { ?>
                No stats available.
            <?php } ?>
        </div>
    </div>
<?php
} ?>

<?php
View::show_footer();
