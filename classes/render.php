<?php 

include './classes/session.php';
include './classes/location.php';

header('Access-Control-Allow-Origin: *');

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Wikia Activity Notifier, it's a webapp that notifies you about recent changes on a FANDOM/Wikia domain.">
    <title>Wikia Acitivity Notifier</title>
    <link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet">
    <link rel="stylesheet" href="./classes/style/app.css">
</head>
<body>
    <div class="app" id="wan">
        <!-- Misc -->
        <div class="unsupportscreensize">
            <h2>Oops!</h2>
            <p>Seems this is a unsupportable screen size (320px)
                this page isn't yet for ultra-smaller devices. We're
                sorry about this.
            </p>
        </div>
        <?php if ($_SESSION['auth'] == true): ?>
        <div class="warpmodal hidden">
            <div class="modal hidden">
                <h3></h3>
                <button id="closemodal">X</button>
                <div class="body"></div>
            </div>
        </div>
        <!-- Main -->
        <div class="navbar">
            <span class="brand-min"><?= $i_BrandMin; ?></span>
            <button id="whatisnew"><?= $i_WhatIsNew; ?></button>
            <button id="aboutwan"><?= $i_AboutWAN; ?></button>
            <button id="faq"><?= $i_FAQ; ?></button>
            <span class="username-min"><?= $_SESSION['username']; ?></span>
        </div>
        <div class="container">
            <h2><?= $i_Brand; ?></h2>
            <div class="actionbuttons">
            <button id="addwiki"><?= $i_AddWiki; ?></button>
            </div>
            <div class="wikislist">
            </div>
        </div>
        <?php else: ?>
        <div class="unauthed">
            <?php if (isset($_GET['failed'])): ?>
            <div class="unauthed-warn-invalid-key"><?= $i_unAuthedInvalid ?></div>
            <?php endif;?>
            <p><?= $i_unAuthedInfo ?></p><br/>
            <div class="unauthed-actions-buttons">
                <button id="app-exit"><?=$i_unAuthedExit; ?></button>
                <button id="app-join"><?=$i_unAuthedJoin; ?></button>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
    <?php if ($_SESSION['auth'] == true): ?>
    <script src="./classes/js/wikia.js"></script>
    <?php endif;?>
    <script src="./i18n/i18n.js"></script>
    <script>const AUTH_STATUS = <?= json_encode($_SESSION['auth']); ?>;</script>
    <script src="./classes/js/auth.js"></script>
    <script>
        wan.preWikis = <?= json_encode($_SESSION['wikis']);?>;
        
        wan.preferedLang = '<?= $_SESSION['lang'];?>';
    </script>
</body>
</html>