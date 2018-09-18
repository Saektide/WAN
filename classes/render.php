<?php 
// Include some PHP classes
include './classes/session.php';
include './classes/location.php';

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Wikia Activity Notifier, it's a webapp that notifies you about recent changes on a FANDOM/Wikia domain.">
    <title>Wikia Acitivity Notifier</title>
    <!-- JQuery CDN -->
    <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
    <!-- Materialize -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Inject site's CSS -->
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
        <div id="modalfixed" class="modal modal-fixed-footer">
            <div class="modal-content">
            <h4></h4>
            <p></p>
            </div>
            <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-light btn-flat">OK</a>
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
            <ul class="wikislist collapsible" data-collapsible="expandable">
            </ul>
        </div>
        
        <?php else: ?>
        <div class="unauthed">
            <?php if (isset($_GET['failed'])): ?>
            <div class="unauthed-warn-invalid-key"><?= $i_unAuthedInvalid ?></div>
            <?php endif;?>
            <p><?= $i_unAuthedInfo ?></p><br/>
            <div class="unauthed-actions-buttons">
                <button id="app-exit" class="waves-effect waves-light btn"><?=$i_unAuthedExit; ?></button>
                <button id="app-join" class="waves-effect waves-light btn modal-trigger" href="#appjoin"><?=$i_unAuthedJoin; ?></button>
            </div>
        </div>
        <!-- App-join module -->
        <div id="appjoin" class="modal">
            <div class="modal-content">
            <h4>AUTH Key</h4>
            <p>Please type your auth key.</p>
            <form action="#" id="appjoin-form" class="col s12">
                <div class="row">
                    <div class="input-field col s12">
                    <input id="auth" type="password" class="validate" required>
                    <label for="auth">Auth</label>
                    </div>
                </div>
                <button href="#!" type="submit" id="appjoin-attemp" class="modal-action waves-effect waves-dark btn-flat">Join</button>
            </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
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