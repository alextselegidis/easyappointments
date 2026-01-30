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

<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>
        <?= lang('appointment_cancelled_title') ?> | Easy!Appointments
    </title>
    <style>
        /* -------------------------------------
            GLOBAL RESETS
        ------------------------------------- */

        /*All the styling goes here*/

        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
        }

        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
        }

        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */

        .body {
            background-color: #f6f6f6;
            width: 100%;
        }

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            margin: 0 auto !important;
            /* makes it centered */
            max-width: 580px;
            padding: 10px;
            width: 580px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 580px;
            padding: 10px;
        }

        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .footer {
            clear: both;
            margin-top: 10px;
            text-align: center;
            width: 100%;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center;
        }

        /* -------------------------------------
            TYPOGRAPHY
        ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
        }

        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px;
        }

        a {
            color: #3498db;
            text-decoration: underline;
        }

        /* -------------------------------------
            BUTTONS
        ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .btn > tbody > tr > td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }

        .btn-primary table td {
            background-color: #3498db;
        }

        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }

        /* -------------------------------------
            OTHER STYLES THAT MIGHT BE USEFUL
        ------------------------------------- */
        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .clear {
            clear: both;
        }

        .mt0 {
            margin-top: 0;
        }

        .mb0 {
            margin-bottom: 0;
        }

        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0;
        }

        .powered-by a {
            text-decoration: none;
        }

        hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            margin: 20px 0;
        }

        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY STYLES
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }

            table.body p,
            table.body ul,
            table.body ol,
            table.body td,
            table.body span,
            table.body a {
                font-size: 16px !important;
            }

            table.body .wrapper,
            table.body .article {
                padding: 10px !important;
            }

            table.body .content {
                padding: 0 !important;
            }

            table.body .container {
                padding: 0 !important;
                width: 100% !important;
            }

            table.body .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }

            table.body .btn table {
                width: 100% !important;
            }

            table.body .btn a {
                width: 100% !important;
            }

            table.body .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }

        /* -------------------------------------
            PRESERVE THESE STYLES IN THE HEAD
        ------------------------------------- */
        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }

            .btn-primary table td:hover {
                background-color: #34495e !important;
            }

            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important;
            }
        }

    </style>
</head>
<body>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">

                <!-- START CENTERED WHITE CONTAINER -->
                <table role="presentation" class="main">

                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>

                                        <!-- Logo at the top center, embedded as CID -->
                                        <img src="cid:logo.png" alt="Logo" style="display:block;max-width:80px;margin: auto auto 24px;">

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


                                            <?php if (!empty($appointment['status'])): ?>
                                            <tr>
                                                <td class="label" style="padding: 3px;font-weight: bold;">
                                                    <?= lang('description') ?>
                                                </td>
                                                <td style="padding: 3px;">
                                                    <?= nl2br(e($service['description'])) ?>
                                                </td>
                                            </tr>
                                            <?php endif; ?>

                                            <?php if (!empty($appointment['location'])): ?>
                                                <tr>
                                                    <td class="label" style="padding: 3px;font-weight: bold;">
                                                        <?= lang('location') ?>
                                                    </td>
                                                    <td style="padding: 3px;">
                                                        <?php if (str_starts_with($appointment['location'], 'http')): ?>
                                                            <a 
                                                                href="<?= e($appointment['location']) ?>" 
                                                                target="_blank">
                                                                <?= e($appointment['location']) ?>
                                                            </a>
                                                        <?php else: ?>
                                                        <?= e($appointment['location']) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <?php if (!empty($appointment['meeting_link'])): ?>
                                                <tr>
                                                    <td class="label" style="padding: 3px;font-weight: bold;">
                                                        <?= lang('meeting_link') ?>
                                                    </td>
                                                    <td style="padding: 3px;">
                                                        <a
                                                            href="<?= e($appointment['meeting_link']) ?>"
                                                            target="_blank">
                                                            <?= e($appointment['meeting_link']) ?>
                                                        </a>
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

                                        <br>

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

                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if (
                                                    setting('display_custom_field_' . $i) &&
                                                    !empty($customer['custom_field_' . $i])
                                                ): ?>
                                                    <tr>
                                                        <td class="label" style="padding: 3px;font-weight: bold;">
                                                            <?= e(
                                                                setting('label_custom_field_' . $i) ?:
                                                                lang('custom_field') . ' #' . $i,
                                                            ) ?>
                                                        </td>
                                                        <td style="padding: 3px;">
                                                            <?= e($customer['custom_field_' . $i]) ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </table>

                                        <br>

                                        <h2>
                                            <?= lang('reason') ?>
                                        </h2>

                                        <p>
                                            <?= e($reason) ?>
                                        </p>


                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- END MAIN CONTENT AREA -->
                </table>

                <!-- END CENTERED WHITE CONTAINER -->

                <!-- START FOOTER -->
                <div class="footer">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="content-block powered-by">
                                Powered by
                                <a href="https://easyappointments.org" style="text-decoration: none;">
                                    Easy!Appointments
                                </a>
                                |
                                <a href="<?= e($settings['company_link']) ?>" style="text-decoration: none;">
                                    <?= e($settings['company_name']) ?>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- END FOOTER -->

            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
</table>

</body>
</html>
