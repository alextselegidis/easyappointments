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
</head>
<body style="font: 13px arial, helvetica, tahoma;">

<div class="email-container" style="width: 650px; border: 1px solid #eee; margin: 30px auto;">
    <div id="header"
         style="background-color: <?= $settings['company_color'] ?? '#429a82' ?>; height: 80px; padding: 15px; text-align: center;">
        <?php if (!empty($settings['company_logo'])): ?>
            <?php if (strpos($settings['company_logo'], 'data:image') === 0): ?>
                <img src="<?= $settings['company_logo'] ?>" alt="<?= e($settings['company_name']) ?>"
                     style="max-height: 50px; max-width: 300px; vertical-align: middle;">
            <?php else: ?>
                <img src="<?= base_url('storage/uploads/' . $settings['company_logo']) ?>"
                     alt="<?= e($settings['company_name']) ?>"
                     style="max-height: 50px; max-width: 300px; vertical-align: middle;">
            <?php endif; ?>
        <?php else: ?>
            <strong id="logo" style="color: white; font-size: 20px; margin-top: 10px; display: inline-block">
                <?= e($settings['company_name']) ?>
            </strong>
        <?php endif; ?>
    </div>

    <div id="content" style="padding: 10px 15px; min-height: 400px">
        <h2>
            <?= $subject ?>
        </h2>
        <p>
            <?= $message ?>
        </p>
    </div>

    <div id="footer" style="padding: 10px; text-align: center; margin-top: 10px;
                border-top: 1px solid #EEE; background: #FAFAFA;">
        <a href="<?= $settings['company_link'] ?>" style="text-decoration: none;">
            <?= e($settings['company_name']) ?>
        </a>
    </div>
</div>

</body>
</html>
