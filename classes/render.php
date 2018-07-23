<?php 

include './classes/session.php';
include './i18n/en.php';
header('Access-Control-Allow-Origin: *');

$q_AppVersion = '1.3';

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
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
        <div class="warpmodal hidden">
            <div class="modal hidden">
                <h3></h3>
                <button id="closemodal">X</button>
                <div class="body"></div>
            </div>
        </div>
        <!-- Main -->
        <div class="navbar">
            <span class="brand-min"><?php echo $i_BrandMin; ?> | <?php echo $q_AppVersion; ?></span>
            <button id="whatisnew">What's New?</button>
            <button id="aboutwan">About WAN</button>
            <button id="faq">FAQ</button>
        </div>
        <div class="container">
            <h2><?php echo $i_Brand; ?></h2>
            <div class="actionbuttons">
            <button id="addwiki">Add wiki</button>
            </div>
            <div class="wikislist">
            </div>
        </div>
    </div>
    <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
    <script src="./classes/js/wikia.js"></script>
    <script src="./classes/js/app.js"></script>
    <script>
    wan.preWikis = <?php echo json_encode($_SESSION['wikis']);?>
    </script>
</body>
</html>