<?php
/**
 * Local variables.
 *
 * @var string $subject
 * @var string $message
 * @var array $appointment
 * @var array $service
 * @var array $provider
 * @var array $customer
 * @var array $settings
 * @var array $timezone
 * @var string $appointment_link
 */
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
  <title><?= lang('appointment_details_title') ?></title>
  <!--[if !mso]><!-->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!--<![endif]-->
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
    #outlook a {
      padding: 0;
    }

    body {
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table,
    td {
      border-collapse: collapse;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }

    p {
      display: block;
      margin: 13px 0;
    }
  </style>
  <!--[if mso]>
        <noscript>
        <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG/>
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        </noscript>
        <![endif]-->
  <!--[if lte mso 11]>
        <style type="text/css">
          .mj-outlook-group-fix { width:100% !important; }
        </style>
        <![endif]-->
  <!--[if !mso]><!-->
  <link href="https://inform-interiors.sfo3.cdn.digitaloceanspaces.com/email/InformType.css" rel="stylesheet" type="text/css">
  <style type="text/css">
    @import url(https://inform-interiors.sfo3.cdn.digitaloceanspaces.com/email/InformType.css);
  </style>
  <!--<![endif]-->
  <style type="text/css">
    @media only screen and (min-width:480px) {
      .mj-column-per-100 {
        width: 100% !important;
        max-width: 100%;
      }

      .mj-column-per-50 {
        width: 50% !important;
        max-width: 50%;
      }
    }
  </style>
  <style media="screen and (min-width:480px)">
    .moz-text-html .mj-column-per-100 {
      width: 100% !important;
      max-width: 100%;
    }

    .moz-text-html .mj-column-per-50 {
      width: 50% !important;
      max-width: 50%;
    }
  </style>
  <style type="text/css">
    @media only screen and (max-width:480px) {
      table.mj-full-width-mobile {
        width: 100% !important;
      }

      td.mj-full-width-mobile {
        width: auto !important;
      }
    }
  </style>
</head>

<body style="word-spacing:normal;background-color:#FFFFFF;">
  <div style="background-color:#FFFFFF;">
    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
        <tbody>
          <tr>
            <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                  <tbody>
                    <tr>
                      <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                      
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                          <tbody>
                            <tr>
                              <td style="width:120px;">
                                <a href="https://booking.inform.ca/" target="_blank" style="color: inherit; text-decoration: none;">
                                  <img alt height="auto" src="https://inform-interiors.sfo3.cdn.digitaloceanspaces.com/email/Inform-logo.png" style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="120">
                                </a>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--[if mso | IE]></td></tr></table><![endif]-->
    
    
    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" bgcolor="#FFFFFF" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#FFFFFF;background-color:#FFFFFF;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#FFFFFF;background-color:#FFFFFF;width:100%;">
        <tbody>
          <tr>
            <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                  <tbody>
                    <tr>
                      <td style="vertical-align:top;padding-bottom:0px;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style width="100%">
                          <tbody>
                            <tr>
                              <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                <div style="font-family:Inform, Helvetica, sans-serif;font-weight:400;font-size:30px;line-height:1.3;text-align:left;color:#000000;"><?= $subject ?></div>
                                <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666; "><p><?= $message ?></p></div>
                              </td>
                            </tr>
                            <tr>
                              <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                <div style="font-family:Inform, Helvetica, sans-serif;font-weight:400;font-size:20px;line-height:1.3;text-align:left;color:#000000;"><?= lang('appointment_details_title') ?></div>
                              </td>
                            </tr>
                            <tr>
                              <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;"> 
                                <table id="appointment-details" border="0" cellpadding="0" cellspacing="0" role="presentation" style width="100%">
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;">Appt Id</div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($appointment['id']) ?>
                                      </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('service') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($service['name']) ?>
                                      </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('provider') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($provider['first_name'] . ' ' . $provider['last_name']) ?>
                                      </div>
                                    </td>
                                </tr>
                                
                                
                                
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('date') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= format_date($appointment['start_datetime']) ?>
                                      </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('time') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= format_time($appointment['start_datetime']) ?>
                                      </div>
                                    </td>
                                </tr>
                                
                              <?php if (!empty($appointment['status'])): ?>
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('status') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($appointment['status']) ?>
                                      </div>
                                    </td>
                                </tr>
                              <?php endif; ?>
                              
                              <?php if (!empty($appointment['location'])): ?>
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('location') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($appointment['location']) ?>
                                      </div>
                                    </td>
                                </tr>
                              <?php endif; ?>
                              
                              <?php if (!empty($service['description'])): ?>
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('description') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($service['description']) ?>
                                      </div>
                                    </td>
                                </tr>
                              <?php endif; ?>

                              <?php
                              $virtual_meeting_display = appointment_virtual_meeting_text($appointment, $provider);
                              ?>
                              <?php if ($virtual_meeting_display !== ''): ?>
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('virtual_meeting') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= nl2br(e($virtual_meeting_display), false) ?>
                                      </div>
                                    </td>
                                </tr>
                              <?php endif; ?>
                                  
                              
                              <?php if (!empty($appointment['notes'])): ?>
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('notes') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($appointment['notes']) ?>
                                      </div>
                                    </td>
                                </tr>
                              <?php endif; ?>
                                

                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--[if mso | IE]></td></tr></table><![endif]-->
    
        
    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" bgcolor="#FFFFFF" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#FFFFFF;background-color:#FFFFFF;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#FFFFFF;background-color:#FFFFFF;width:100%;">
        <tbody>
          <tr>
            <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                  <tbody>
                    <tr>
                      <td style="vertical-align:top;padding-bottom:0px;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style width="100%">
                          <tbody>
                            <tr>
                              <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                <div style="font-family:Inform, Helvetica, sans-serif;font-weight:400;font-size:20px;line-height:1.3;text-align:left;color:#000000;"><?= lang('customer_details_title') ?></div>
                              </td>
                            </tr>
                            <tr>
                              <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;"> 
                                <table id="customer-details" border="0" cellpadding="0" cellspacing="0" role="presentation" style width="100%">
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('name') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($customer['first_name'] . ' ' . $customer['last_name']) ?>
                                      </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('email') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($customer['email']) ?>
                                      </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="label" style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#666666;"><?= lang('phone_number') ?></div>
                                    </td>
                                    <td style="padding: 3px 0px; width: 50%;">
                                      <div style="font-family:Inform, Helvetica, sans-serif;font-size:16px;line-height:1.3;text-align:left;color:#000000;">
                                        <?= e($customer['phone_number']) ?>
                                      </div>
                                    </td>
                                </tr>
    
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--[if mso | IE]></td></tr></table><![endif]-->
    
    
    
    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" bgcolor="#FFFFFF" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#FFFFFF;background-color:#FFFFFF;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#FFFFFF;background-color:#FFFFFF;width:100%;">
        <tbody>
          <tr>
            <td style="direction:ltr;font-size:0px;padding:40px 0 20px 0;padding-top:40px;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                  <tbody>
                    <tr>
                      <td style="vertical-align:top;padding-top:0px;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style width="100%">
                          <tbody>
                            <tr>
                              <td align="center" vertical-align="middle" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;width:100%;line-height:100%;">
                                  <tr>
                                      <td align="center" bgcolor="#000000" role="presentation" style="border:none;border-radius:0px;cursor:auto;mso-padding-alt:20px 25px 20px 25px;background:#000000;" valign="middle">
                                        <?php
                                        $inform_craft_checkout_base = 'https://staging.inform.ca/shop/services/design-consultation/';
                                        $inform_craft_checkout_href =
                                            $inform_craft_checkout_base .
                                            '?' .
                                            http_build_query(
                                                [
                                                    'appointmentId' => $appointment['id'],
                                                    'googleCalendarUrl' => $add_to_google_url,
                                                ],
                                                '',
                                                '&',
                                                PHP_QUERY_RFC3986,
                                            );
                                        ?>
                                        <a href="<?= e($inform_craft_checkout_href) ?>" style="display: inline-block; background: #000000; color: #FFFFFF; font-family: Inform, Helvetica, sans-serif; font-size: 20px; font-weight: normal; line-height: 120%; margin: 0; text-decoration: none; text-transform: uppercase; padding: 20px 25px 20px 25px; mso-padding-alt: 0px; border-radius: 0px; width: 100%;" target="_blank">Complete payment</a>
                                      </td>
                                    </tr>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td style="vertical-align:top;padding-top:0px;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style width="100%">
                          <tbody>
                            <tr>
                              <td align="center" vertical-align="middle" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;width:100%;line-height:100%;">
                                  <tr>
                                    <td align="center" bgcolor="#FFFFFF" role="presentation" style="border:none;border-radius:0px;cursor:auto;mso-padding-alt:20px 25px 20px 25px;background:#000000;" valign="middle">
                                      <a href="<?= e($appointment_link) ?>" style="display: inline-block; background: #FFFFFF; color: #000000; font-family: Inform, Helvetica, sans-serif; font-size: 20px; font-weight: normal; line-height: 120%; margin: 0; text-decoration: none; text-transform: uppercase; padding: 20px 25px 20px 25px; mso-padding-alt: 0px; border: 1px solid #000000; border-radius: 0px; width: 100%;" target="_blank"><?= lang('appointment_link_title') ?></a>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--[if mso | IE]></td></tr></table><![endif]-->
    
    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" bgcolor="#FFFFFF" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#FFFFFF;background-color:#FFFFFF;margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#FFFFFF;background-color:#FFFFFF;width:100%;">
          <tbody>
            <tr>
              <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                    <tbody>
                      <tr>
                        <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                          <p style="border-top:solid 1px #AAAAAA;font-size:1px;margin:0px auto;width:100%;">
                          </p>
                          <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:solid 1px #AAAAAA;font-size:1px;margin:0px auto;width:550px;" role="presentation" width="550px" ><tr><td style="height:0;line-height:0;"> &nbsp;
    </td></tr></table><![endif]-->
                        </td>
                      </tr>
                      <tr>
                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                          <div style="font-family:Inform, Helvetica, sans-serif;font-size:14px;line-height:1.3;text-align:left;color:#666666;">If you have any questions, reply to this email or contact us at design@inform.ca</div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!--[if mso | IE]></td></tr></table><![endif]-->
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    <!--[if mso | IE]></td></tr></table><![endif]-->
    
  </div>
</body>

</html>
