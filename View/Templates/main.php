<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=$title?></title>

    <!-- Bootstrap core CSS -->
    <link href="/vendor/twitter/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link href="/Assets/css/libs.min.css" rel="stylesheet">
    <link href="/Assets/css/written.min.css" rel="stylesheet">

</head>

<body>

<!-- Navigation -->
<header>
    <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
            <a href="#" class="navbar-brand d-flex align-items-left">
                <strong>HEAT.IO</strong>
            </a>
            <a href="/" class="navbar-brand d-flex align-items-center">
                <i class="fa fa-desktop"></i> &nbsp; Dashboard
            </a>
            <a href="/heating" class="navbar-brand d-flex align-items-center <?=$heatingStatus?'text-danger':''?>">
                <i class="fa fa-fire"></i> &nbsp; Heating <?=$heatingStatus?'(ON)':'(OFF)'?>
            </a>
            <a href="/hotwater" class="navbar-brand d-flex align-items-center <?=$hwStatus?'text-warning':''?>">
                <i class="fa fa-shower"></i> &nbsp; Hotwater  <?=$hwStatus?'(ON)':'(OFF)'?>
            </a>
            <a href="/settings" class="navbar-brand d-flex align-items-center">
                <i class="fa fa-cog"></i> &nbsp; Settings
            </a>

        </div>
    </div>
</header>

<!-- Page Content -->
<main role="main"><?=$existingText?>
</main>


<script src="/Assets/js/jq.min.js"></script>
<script src="/vendor/twitter/bootstrap/dist/js/bootstrap.bundle.js"></script>

</body>

</html>