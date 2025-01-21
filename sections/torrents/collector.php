<?php
if (!check_perms('zip_downloader')) {
    error(403);
}

if (empty($_GET['ids']) || empty($_GET['title'])) {
    error(0);
}

$ids = explode(',', $_GET['ids']);
foreach ($ids as $id) {
    if ((int)$id < 1) {
        error(0);
    }
}
$title = trim($_GET['title']);

$query = $DB->prepared_query(sprintf('
    SELECT
        t.Id AS TorrentID,
        t.GroupID,
        t.Processing,
        t.Codec,
        t.Source,
        t.Resolution,
        t.Container
        tg.Year AS Year,
        tg.Name,
        tg.SubName,
        t.Size
    FROM torrents t
    INNER JOIN torrents_group tg ON (t.GroupID = tg.ID)
    WHERE t.ID IN (%s)', placeholders($ids)), ...$ids);

$collector = new TorrentsDL($query, $title);
$filer = new Gazelle\File\Torrent;
while ([$downloads, $groupIds] = $collector->get_downloads('TorrentID')) {
    $artists = Artists::get_artists($groupIds);
    $torrentIds = array_keys($groupIds);
    $fileQuery = $DB->prepared_query(sprintf(
        '
        SELECT ID FROM torrents WHERE ID IN (%s)',
        placeholders($torrentIds)
    ), ...$torrentIds);

    if (is_int($fileQuery)) {
        foreach ($torrentIds as $id) {
            $download = &$downloads[$id];
            $download['Artist'] = Artists::display_artists($artists[$download['GroupID']], false, true, false);
            $collector->fail_file($download);
        }
        continue;
    }

    while ([$id] = $DB->next_record(MYSQLI_NUM, false)) {
        $download = &$downloads[$id];
        $download['Artist'] = Artists::display_artists($artists[$download['GroupID']], false, true, false);
        $collector->add_file($filer->get($id), $download);
        unset($download);
    }
}

$collector->finalize(false);

define('SKIP_NO_CACHE_HEADERS', 1);
