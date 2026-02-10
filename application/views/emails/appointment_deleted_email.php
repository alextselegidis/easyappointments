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
    <title><?= lang('appointment_cancelled_title') ?><?= !empty($settings['company_name']) ? ' | ' . e($settings['company_name']) : '' ?></title>
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

    <div id="content" style="padding: 10px 15px; min-height: 400px;">
        <h2>
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

        <?php if (!empty($custom_fields)): ?>
            <h2>
                <?= lang('custom_fields') ?>
            </h2>

            <table id="custom-fields-details">
                <?php foreach ($custom_fields as $field): ?>
                    <tr>
                        <td class="label" style="padding: 3px;font-weight: bold;">
                            <?= e($field['label']) ?>
                        </td>
                        <td style="padding: 3px;">
                            <?= e($field['value']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <h2>
            <?= lang('reason') ?>
        </h2>

        <p>
            <?= e($reason) ?>
        </p>
    </div>

    <div id="footer" style="padding: 10px; text-align: center; margin-top: 10px;
                border-top: 1px solid #EEE; background: #FAFAFA;">
        <a href="<?= e($settings['company_link']) ?>" style="text-decoration: none;">
            <?= e($settings['company_name']) ?>
        </a>
    </div>
</div>

</body>
</html>
