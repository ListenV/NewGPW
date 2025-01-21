<?php
header('Content-type: application/opensearchdescription+xml');
require __DIR__ . '/../classes/config.php'; // The config contains all site-wide configuration information as well as memcached rules
require __DIR__ . '/../classes/const.php';

$SSL = ($_SERVER['SERVER_PORT'] === '443');

$Type = ((!empty($_GET['type']) && in_array($_GET['type'], array('torrents', 'artists', 'requests', 'forums', 'users', 'wiki', 'log'))) ? $_GET['type'] : 'artists');

echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
    <ShortName><?= CONFIG['SITE_NAME'] . ' ' . ucfirst($Type) ?> </ShortName>
    <Description>Search <?= CONFIG['SITE_NAME'] ?> for <?= ucfirst($Type) ?></Description>
    <Developer></Developer>
    <Image width="16" height="16" type="image/x-icon"><?= CONFIG['SITE_URL'] ?>/favicon.ico</Image>
    <?php
    switch ($Type) {
        case 'artists':
    ?>
            <Url type="text/html" method="get" template="<?= CONFIG['SITE_URL'] ?>/artist.php?artistname={searchTerms}"></Url>
            <moz:SearchForm><?= CONFIG['SITE_URL'] ?>/torrents.php?action=advanced</moz:SearchForm>
        <?php
            break;
        case 'torrents':
        ?>
            <Url type="text/html" method="get" template="<?= CONFIG['SITE_URL'] ?>/torrents.php?action=basic&amp;searchstr={searchTerms}"></Url>
            <moz:SearchForm><?= CONFIG['SITE_URL'] ?>/torrents.php</moz:SearchForm>
        <?php
            break;
        case 'requests':
        ?>
            <Url type="text/html" method="get" template="<?= CONFIG['SITE_URL'] ?>/requests.php?search={searchTerms}"></Url>
            <moz:SearchForm><?= CONFIG['SITE_URL'] ?>/requests.php</moz:SearchForm>
        <?php
            break;
        case 'forums':
        ?>
            <Url type="text/html" method="get" template="<?= CONFIG['SITE_URL'] ?>/forums.php?action=search&amp;search={searchTerms}"></Url>
            <moz:SearchForm><?= CONFIG['SITE_URL'] ?>/forums.php?action=search</moz:SearchForm>
        <?php
            break;
        case 'users':
        ?>
            <Url type="text/html" method="get" template="<?= CONFIG['SITE_URL'] ?>/user.php?action=search&amp;search={searchTerms}"></Url>
            <moz:SearchForm><?= CONFIG['SITE_URL'] ?>/user.php?action=search</moz:SearchForm>
        <?php
            break;
        case 'wiki':
        ?>
            <Url type="text/html" method="get" template="<?= CONFIG['SITE_URL'] ?>/wiki.php?action=search&amp;search={searchTerms}"></Url>
            <moz:SearchForm><?= CONFIG['SITE_URL'] ?>/wiki.php?action=search</moz:SearchForm>
        <?php
            break;
        case 'log':
        ?>
            <Url type="text/html" method="get" template="<?= ($SSL ? 's' : '') ?>://<?= CONFIG['SITE_URL'] ?>/log.php?search={searchTerms}"></Url>
            <moz:SearchForm><?= CONFIG['SITE_URL'] ?>/log.php</moz:SearchForm>
    <?php
            break;
    }
    ?>
    <Url type="application/opensearchdescription+xml" rel="self" template="<?= CONFIG['SITE_URL'] ?>/opensearch.php?type=<?= $Type ?>" />
    <Language>en-us</Language>
    <OutputEncoding>UTF-8</OutputEncoding>
    <InputEncoding>UTF-8</InputEncoding>
</OpenSearchDescription>