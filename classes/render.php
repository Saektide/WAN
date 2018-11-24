<?php 
// Include some PHP classes
include './classes/session.php';
include './classes/location.php';

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Wiki Activity Notifier, it's a webapp that notifies you about recent changes on a domain that has MediaWiki.">
    <title>Wiki Acitivity Notifier</title>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-120921844-2"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-120921844-2');
    </script>
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
        <?php if ($_SESSION['auth'] == true or $onDevRelease): ?>
        <div id="modalfixed" class="modal modal-fixed-footer">
            <div class="modal-content">
            <h4></h4>
            <p></p>
            </div>
            <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-light btn-flat"><?= $i_Close ?></a>
            </div>
        </div>
        <!-- Main -->
        <div class="navbar">
            <span class="brand-min"><?= $i_BrandMin; ?></span>
            <span class="username-min">
            <?php if ($onDevRelease): ?>
            Developer release
            <?php else: ?>
            <?= $_SESSION['username']; ?>
            <?php endif; ?>
            <a href="#" data-activates="wan-menu" class="open-wan-menu"><i class="material-icons">menu</i></a>
            </span>
        </div>
        <div class="container">
            <ul class="wikislist collapsible" data-collapsible="expandable">
            </ul>
        </div>
        <!-- Sidenav -->
        <ul id="wan-menu" class="side-nav">
            <li><div class="user-view">
            <span class="name">
            <?php if ($onDevRelease): ?>
            WAN
            <?php else: ?>
            <?= $_SESSION['username']; ?>
            <?php endif; ?>
            </span>
            </div></li>
            <li><a class="waves-effect" href="#" id="addwiki"><i class="material-icons">add</i><?= $i_AddWiki; ?></a></li>
            <li><div class="divider"></div></li>
            <li><a class="waves-effect" href="#" id="wansettings"><i class="material-icons">settings</i><?= $i_Settings; ?></a></li>
            <li><a class="waves-effect" href="#" id="whatisnew"><i class="material-icons">update</i><?= $i_WhatIsNew; ?></a></li>
            <li><a class="waves-effect" href="#" id="aboutwan"><i class="material-icons">info</i><?= $i_AboutWAN; ?></a></li>
            <li><a class="waves-effect" href="#" id="faq"><i class="material-icons">help</i><?= $i_FAQ; ?></a></li>
            <li><a class="waves-effect" href="#" id="logout"><i class="material-icons">exit_to_app</i><?= $i_Logout; ?></a></li>
        </ul>
        <?php else: ?>
        <div class="unauthed">
            <?php if (isset($_GET['failed'])): ?>
            <div class="unauthed-warn-invalid-key"><?= $i_unAuthedInvalid ?></div>
            <?php endif;?>
            <p><?= $i_unAuthedInfo ?></p><br/>
            <div class="unauthed-actions-buttons">
                <button id="app-join" class="waves-effect waves-light btn modal-trigger" href="#appjoin"><?=$i_unAuthedJoin; ?></button>
            </div>
        </div>
        <!-- App-join module -->
        <div id="appjoin" class="modal">
            <div class="modal-content">
            <h4><?= $i_unAuthedFormTitle ?></h4>
            <p><?= $i_unAuthedFormInfo ?></p>
            <form action="#" id="appjoin-form" class="col s12">
                <div class="row">
                    <div class="input-field col s12">
                    <input id="auth" type="password" required>
                    <label for="auth"><?= $i_unAuthedFormAuth ?></label>
                    </div>
                </div>
                <button href="#!" type="submit" id="appjoin-attemp" class="modal-action waves-effect waves-dark btn-flat"><?= $i_unAuthedFormSubmit ?></button>
            </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php if ($_SESSION['auth'] == true): ?>
    <script src="./classes/js/mw.js"></script>
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