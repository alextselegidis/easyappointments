<?php
/**
 * Local variables.
 *
 * @var array $appointment
 * @var array $service
 * @var array $provider
 * @var array $customer
 * @var array $settings
 * @var array $timezone
 * @var string $reason
 */
?>

<html lang="en">
<head>
    <title><?= lang('appointment_cancelled_title') ?> | Easy!Appointments</title>
    <style>
        body{ background:#f5f7f8; margin:0; padding:20px; color:#333; }
        .email-container{ max-width:650px; margin:30px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #eee; font-family: Arial, Helvetica, sans-serif; }
        .email-header{ height:60px; padding:14px 20px; display:flex; align-items:center; }
        .email-logo{ color:#fff; font-size:20px; font-weight:700; text-decoration:none; }
        .email-content{ padding:20px; }
        h2{ color:#222; font-size:18px; margin:0 0 10px; }
        .subject-title{ color: #1a73e8; /* accent color for subject */ }
        p{ line-height:1.45; margin:0 0 12px; }
        table{ width:100%; border-collapse:collapse; margin-bottom:12px; }
        td.label{ width:170px; color:#555; font-weight:700; padding:6px 0; vertical-align:top; }
        td{ padding:6px 0; vertical-align:top; }
        .email-footer{ padding:12px 20px; text-align:center; border-top:1px solid #eee; background:#fafafa; font-size:13px; color:#777; }
        .muted{ color:#777; font-size:13px; }
    </style>
</head>
<body>

<div class="email-container">
    <div id="header" class="email-header" style="background-color: <?= $settings['company_color'] ?? '#429a82' ?>;">
        <strong id="logo" class="email-logo">
            <?= e($settings['company_name']) ?>
        </strong>
    </div>

    <div id="content" class="email-content" style="min-height:400px;">
        <h2 class="subject-title">
            <?= lang('appointment_cancelled_title') ?>
        </h2>

        <p>
            <?= lang('appointment_removed_from_schedule') ?>
        </p>

        <h2>
            <?= lang('appointment_details_title') ?>
        </h2>

        <table id="appointment-details">
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('service') ?>
                </td>
                <td style="padding: 3px;">
                    <?= e($service['name']) ?>
                </td>
            </tr>
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('provider') ?>
                </td>
                <td style="padding: 3px;">
                    <?= e($provider['first_name'] . ' ' . $provider['last_name']) ?>
                </td>
            </tr>
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('start') ?>
                </td>
                <td style="padding: 3px;">
                    <?= format_date_time($appointment['start_datetime']) ?>
                </td>
            </tr>
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('end') ?>
                </td>
                <td style="padding: 3px;">
                    <?= format_date_time($appointment['end_datetime']) ?>

                </td>
            </tr>
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('timezone') ?>
                </td>
                <td style="padding: 3px;">
                    <?= format_timezone($timezone) ?>
                </td>
            </tr>

            <?php if (!empty($appointment['status'])): ?>
                <tr>
                    <td class="label" style="padding: 3px;font-weight: bold;">
                        <?= lang('status') ?>
                    </td>
                    <td style="padding: 3px;">
                        <?= e($appointment['status']) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('description') ?>
                </td>
                <td style="padding: 3px;">
                    <?= e($service['description']) ?>
                </td>
            </tr>

            <?php if (!empty($appointment['location'])): ?>
                <tr>
                    <td class="label" style="padding: 3px;font-weight: bold;">
                        <?= lang('location') ?>
                    </td>
                    <td style="padding: 3px;">
                        <?= e($appointment['location']) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if (!empty($appointment['notes'])): ?>
                <tr>
                    <td class="label" style="padding: 3px;font-weight: bold;">
                        <?= lang('notes') ?>
                    </td>
                    <td style="padding: 3px;">
                        <?= e($appointment['notes']) ?>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <h2>
            <?= lang('customer_details_title') ?>
        </h2>

        <table id="customer-details">
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('name') ?>
                </td>
                <td style="padding: 3px;">
                    <?= e($customer['first_name'] . ' ' . $customer['last_name']) ?>
                </td>
            </tr>
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('email') ?>
                </td>
                <td style="padding: 3px;">
                    <?= e($customer['email']) ?>
                </td>
            </tr>
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('phone_number') ?>
                </td>
                <td style="padding: 3px;">
                    <?= e($customer['phone_number']) ?>
                </td>
            </tr>
            <tr>
                <td class="label" style="padding: 3px;font-weight: bold;">
                    <?= lang('address') ?>
                </td>
                <td style="padding: 3px;">
                    <?= e($customer['address']) ?>
                </td>
            </tr>
        </table>

        <h2>
            <?= lang('reason') ?>
        </h2>

        <p>
            <?= e($reason) ?>
        </p>
    </div>

    <div id="footer" class="email-footer">
        Powered by
        <a href="https://easyappointments.org" style="text-decoration: none;">
            Easy!Appointments
        </a>
        |
        <a href="<?= e($settings['company_link']) ?>" style="text-decoration: none;">
            <?= e($settings['company_name']) ?>
        </a>
    </div>
</div>

</body>
</html>
