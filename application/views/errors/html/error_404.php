<?php
/**
 * @var string $heading
 * @var string $message
 */
?>

<!doctype html>
<html lang="en" style="
    height: 100%;
">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title>404 Page Not Found | Easy!Appointments</title>
    <style>
        #error-container {
            background: #ffffff;
            min-width: 450px;
            max-width: 600px;
            margin: auto;
            border: 1px solid #D0D0D0;
            font: 13px/20px normal Helvetica, Arial, sans-serif;
            color: #4F5155;
        }

        #error-container a {
            color: #003399;
            background-color: transparent;
            font-weight: normal;
        }

        #error-container h1 {
            color: #444;
            background-color: transparent;
            border-bottom: 1px solid #D0D0D0;
            font-size: 19px;
            font-weight: normal;
            margin: 0 0 14px 0;
            padding: 20px;
        }

        #error-container code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 12px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 14px 0 14px 0;
            padding: 20px;
        }

        #error-container p {
            margin: 20px;
        }
    </style>
</head>
<body style="
    height: 100%;
    padding: 0;
    margin: 0;
    display: flex;
    background: #f5f8fa;
">
<div id="error-container">
    <h1>
        <?= $heading ?>
    </h1>

    <?= $message ?>

    <p>
        <small>
            Powered by
            <a href="https://easyappointments.org">Easy!Appointments</a>
        </small>
    </p>
</div>
</body>
</html>
