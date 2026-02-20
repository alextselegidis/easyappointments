<?php
/**
 * Local variables.
 *
 * @var string $subject
 * @var string $message
 * @var array $settings
 */
?>
<html lang="en">
<head>
    <title><?= $subject ?> | Easy!Appointments</title>
    <style>
        body{ background:#f5f7f8; margin:0; padding:20px; color:#333; }
        .email-container{ max-width:650px; margin:30px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #eee; font-family: Arial, Helvetica, sans-serif; }
        .email-header{ height:60px; padding:14px 20px; display:flex; align-items:center; }
        .email-logo{ color:#fff; font-size:20px; font-weight:700; text-decoration:none; }
        .email-content{ padding:20px; }
        h2{ color:#222; font-size:18px; margin:0 0 10px; }
        .subject-title{ color: #1a73e8; /* accent color for subject */ }
        p{ line-height:1.45; margin:0 0 12px; }
        .email-footer{ padding:12px 20px; text-align:center; border-top:1px solid #eee; background:#fafafa; font-size:13px; color:#777; }
    </style>
</head>
<body>

<div class="email-container">
    <div id="header" class="email-header" style="background-color: <?= $settings['company_color'] ?? '#429a82' ?>;">
        <strong id="logo" class="email-logo">
            <?= e($settings['company_name']) ?>
        </strong>
    </div>

    <div id="content" class="email-content" style="min-height:400px">
        <h2 class="subject-title">
            <?= $subject ?>
        </h2>
        <p>
            <?= $message ?>
        </p>
    </div>
    <div id="footer" class="email-footer">
        Powered by
        <a href="https://easyappointments.org" style="text-decoration: none;">
            Easy!Appointments
        </a>
        |
        <a href="<?= $settings['company_link'] ?>" style="text-decoration: none;">
            <?= e($settings['company_name']) ?>
        </a>
    </div>
</div>

</body>
</html>
