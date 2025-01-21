<?php

if (!check_perms('users_mod')) {
    error(403);
}
$Title = "Artist Importance Sandbox";
View::show_header($Title, 'bbcode_sandbox');
$DB->prepared_query("SELECT ag.Name as ArtistName, tg.Name as GroupName, ta.Importance, ta.GroupID, ta.ArtistID FROM torrents_artists AS ta LEFT JOIN torrents_group AS tg ON tg.ID = ta.GroupID LEFT JOIN artists_group AS ag ON ag.ArtistID = ta.ArtistID WHERE ta.Importance='' ORDER BY ta.ArtistID, ta.GroupID");

?>
<div class="BodyHeader">
    <h2 class="BodyHeader-nav"><?= $Title ?></h2>
</div>
<div class="thin BoxBody">
    <table>
        <tr>
            <th>Artist Name</th>
            <td>Group Name</td>
            <td>Importance</td>
            <td>Artist Link</td>
            <td>Group Link</td>
        </tr>
        <?php

        while (list($ArtistName, $GroupName, $Importance, $GroupID, $ArtistID) = $DB->fetch_record()) {
        ?>
            <tr>
                <td><?= $ArtistName ?></td>
                <td><?= $GroupName ?></td>
                <td><?= var_export($Importance, true) ?></td>
                <td>https://orpheus.network/torrents.php?id=<?= $GroupID ?></td>
                <td>https://orpheus.network/artist.php?id=<?= $ArtistID ?></td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
<?
View::show_footer();
