<!DOCTYPE html>
<html>
<?php
	
	$this->load->model('settings_model');			
	$theme_color = $this->settings_model->get_setting('theme_color');
?>
	<style>

	#wait.item {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    margin: auto;
	}
	
	#wait img {
    width: 150px;
    height: 150px;
    position: absolute;
    left: 0px;
    right: 0;
    top: 0px;
    bottom: 0;
    margin: auto;
	}
	
	.centered {
	  position: fixed;
	  left: 50%;
	  margin-left: -170px;
	}	
	
	</style> 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">

    <title><?= lang('page_title') . ' ' .  $company_name ?></title>
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.custom.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/cookieconsent/cookieconsent.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/frontend_' . $theme_color . '.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general_' . $theme_color . '.css'); ?>">

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="icon" sizes="192x192" href="<?= asset_url('assets/img/logo.png') ?>">
</head>

<body>

                <?php
                    if ($manage_mode === TRUE) {
					$this->load->model('settings_model');			
					$time_format = $this->settings_model->get_setting('time_format');
					$date_format = $this->settings_model->get_setting('date_format');
		
						switch($date_format) {
							case 'DMY':
								$dateview='d/m/Y';
								break;
							case 'MDY':
								$dateview='m/d/Y';
								break;
							case 'YMD':
								$dateview='Y/m/d';
								break;
							default:
								$dateview='Y/m/d';
								break;
						}			
				
						switch($time_format) {
							case 'military':
								$timeview=' H:i';
								break;
							case 'regular':
								$timeview='g:i a';
								break;
							default:
								$timeview=' H:i';
								break;
						}			
			
					$longDay = $this->lang->line(strtolower(date('l',strtotime($appointment_data['start_datetime']))));
					$date_field = date($dateview,strtotime($appointment_data['start_datetime']));
					$time_field = date($timeview,strtotime($appointment_data['start_datetime']));
					if ($time_format == 'military') {				
						$appt_date = $date_field . $time_field;
					} else {
						$appt_date = $longDay . ', ' . $date_field . $time_field;
					}			
                        echo '
							<div id="wizard-frame-0" class="wizard-frame" >
								<h3>' .
                                         $this->lang->line('what_to_do') .
                                '<h3>
								<div button id="selectNew" class="btn btn-buttonanew btn-primary" value=0 data-step_index="0" class="col-md-6">'. $this->lang->line('new_apt') .'<i class="icon-forward icon-white"></i></button>
								</div>
								<br><br>									
								<div button type="button" id="button-next-0" class="btn button-next btn-primary" 
								data-step_index="0" class="col-md-6">'. $this->lang->line('change_apt') .'<i class="icon-forward icon-white"></i></button>
								</div> 
								<br><strong><h5><div id="appointment-service"></div></h5></strong>
								<h6>'.$appt_date.'</h6><br>
								
								<form id="cancel-appointment-form" method="post"
										action="' . $this->config->item('base_url')
										. '/index.php/appointments/cancel/' . $appointment_data['hash'] . '" >
									<input type="hidden" name="csrfToken" value="' . $this->security->get_csrf_hash() . '" />
									<button id="cancel-appointment" style="color:white; background-color:red;" class="btn btn-default">' . $this->lang->line('apointent_cancelation') . ' </button>
									<h6>' . $this->lang->line('write_appointment_removal_reason') . '</h6>
									<textarea name="cancel_reason" required></textarea>
								</form>
								
								<div id="remove-personal-info">
									<button id="delete-personal-information" class="btn btn-danger btn-sm">'.$this->lang->line('delete_personal_information_hint').'</button>
								</div>
							</div>
                            <div id="cancel-appointment-frame" class="row">
                                <div class="col-xs-12 col-sm-9">
									<div align="center" style="margin-left: 30px;">
										<h6>'.$this->lang->line('change_apt').'<br>'.$appt_date.'<h6>
									</div> 
                                </div>
								<div >
                                    <form id="update-appointment-form" method="post"
                                            action="' . $this->config->item('base_url')
                                            . '/index.php/appointments/index/' . $appointment_data['hash'] . '">
                                    <input type="hidden" name="csrfToken" value="' . $this->security->get_csrf_hash() . '" />

                                    <button id="return_to_start" style="color:white; background-color:red;" class="btn btn-default">'. $this->lang->line('return_to_start') .'</button>
                                    </form>
                                </div>
                            </div>';
                    }
                ?>

                <?php
                    if (isset($exceptions)) {
                        echo '<div style="margin: 10px">';
                        echo '<h4>' . lang('unexpected_issues') . '</h4>';
                        foreach($exceptions as $exception) {
                            echo exceptionToHtml($exception);
                        }
                        echo '</div>';
                    }
                ?>

                <!-- SELECT SERVICE AND PROVIDER -->

                <div id="wizard-frame-1" class="wizard-frame" style="display:none" >
                    <div class="frame-container">
                        <h3 class="frame-title"><?= lang('step_one_title') ?></h3>

                        <div class="frame-content">
                            <div class="form-group">
                                <label for="select-service">
                                    <strong><?= lang('select_service') ?></strong>
                                </label>

                                <select id="select-service" class="col-xs-12 col-sm-4 form-control">
                                    <?php
                                        // Group services by category, only if there is at least one service with a parent category.
                                        $has_category = FALSE;
                                        foreach($available_services as $service) {
                                            if ($service['category_id'] != NULL) {
                                                $has_category = TRUE;
                                                break;
                                            }
                                        }

                                        if ($has_category) {
                                            $grouped_services = array();

                                            foreach($available_services as $service) {
                                                if ($service['category_id'] != NULL) {
                                                    if (!isset($grouped_services[$service['category_name']])) {
                                                        $grouped_services[$service['category_name']] = array();
                                                    }

                                                    $grouped_services[$service['category_name']][] = $service;
                                                }
                                            }

                                            // We need the uncategorized services at the end of the list so
                                            // we will use another iteration only for the uncategorized services.
                                            $grouped_services['uncategorized'] = array();
                                            foreach($available_services as $service) {
                                                if ($service['category_id'] == NULL) {
                                                    $grouped_services['uncategorized'][] = $service;
                                                }
                                            }

                                            foreach($grouped_services as $key => $group) {
                                                $group_label = ($key != 'uncategorized')
                                                        ? $group[0]['category_name'] : 'Uncategorized';

                                                if (count($group) > 0) {
                                                    echo '<optgroup label="' . $group_label . '">';
                                                    foreach($group as $service) {
                                                        echo '<option value="' . $service['id'] . '">'
                                                            . $service['name'] . '</option>';
                                                    }
                                                    echo '</optgroup>';
                                                }
                                            }
                                        }  else {
                                            foreach($available_services as $service) {
                                                echo '<option value="' . $service['id'] . '">' . $service['name'] . '</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group" id="selectprovider">
                                <label for="select-provider">
                                    <strong><?= lang('select_provider') ?></strong>
                                </label>

                                <select id="select-provider" class="col-xs-12 col-sm-4 form-control"></select>
                            </div>

                            <div id="service-description" style="display:none;"></div>
                        </div>
                    </div>
                </div>

                <!-- SELECT APPOINTMENT DATE -->
					

					<div class="centered" style="width:340px">
	                    <div align="center"><h3 style="text-align: center"><?= lang('check_availability'); ?></h3></div>
						<div id="myMessage" align="center";>
							<h6 style= "margin-left:20px; margin-right:20px;"><?= lang('check_availability_msg'); ?><h6>
						</div>
						<div align="center" id="select-date" >
							<figure id="wait" class="item">
								<img id='spinner' src="<?= $this->config->item('base_url'); ?>/assets/img/loading.gif" />
							</figure>
						</div>
						<div align="center" style="padding:10px;">
							<a href="https://<?php	echo $_SERVER['SERVER_NAME'];?>/wordpress/register/" target="_parent" class="btn button-waitinglist btn-primary">
								<?= lang('register'); ?></a>
							<a href="https://<?php echo $_SERVER['SERVER_NAME'];?>/wordpress/login/" target="_parent" class="btn button-waitinglist btn-primary">
							<?= lang('login'); ?></a>
							<button id="insert-waitinglist" class="btn button-waitinglist btn-primary" style="margin:1px" data-step_index="2"
								title="<?= lang('check_availability'); ?>">
								<span class="glyphicon glyphicon-time"></span>
								<?= lang('waiting_list'); ?>
							</button>
							<div id="available-hours"></div>
						</div>							
					</div>

								
									
									


    <?php require 'waiting_list_modal.php' ?>
    <?php if ($display_cookie_notice === '1'): ?>
        <?php require 'cookie_notice_modal.php' ?>
    <?php endif ?>



    <script>
        var GlobalVariables = {
            availableServices   : <?= json_encode($available_services) ?>,
            availableProviders  : <?= json_encode($available_providers) ?>,
            baseUrl             : <?= json_encode(config('base_url')) ?>,
            manageMode          : <?= $manage_mode ? 'true' : 'false' ?>,
            customerToken       : <?= json_encode($customer_token) ?>,
            dateFormat          : <?= json_encode($date_format) ?>,
            timeFormat          : <?= json_encode($time_format) ?>,
            displayCookieNotice : <?= json_encode($display_cookie_notice === '1') ?>,
            appointmentData     : <?= json_encode($appointment_data) ?>,
            providerData        : <?= json_encode($provider_data) ?>,
            customerData        : <?= json_encode($customer_data) ?>,
            csrfToken           : <?= json_encode($this->security->get_csrf_hash()) ?>,
			confNotice			: <?= json_encode($conf_notice) ?>,
			maxDate    			: <?= json_encode($max_date) ?>,
			wpInvoice			: <?= json_encode($wp_invoice) ?>,
			sessionId			: <?= json_encode($session_id) ?>,
			seeAt				: <?= json_encode($this->lang->line('wp_invoice_see_at')) ?>,
			hideProvider		: <?= json_encode($hide_provider) ?>
        };

        var EALang = <?= json_encode($this->lang->language) ?>;
        var availableLanguages = <?= json_encode($this->config->item('available_languages')) ?>;
    </script>

    <script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/cookieconsent/cookieconsent.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/datejs/date.js') ?>"></script>
    <script src="<?= asset_url('assets/js/frontend_book_api.js') ?>"></script>
    <script src="<?= asset_url('assets/js/frontend_book.js') ?>"></script>

    <script>
        $(document).ready(function() {
            FrontendBook.initialize(true, GlobalVariables.manageMode);
            GeneralFunctions.enableLanguageSelection($('#select-language'));
        });
		
		if ($("#available-hours").html() === EALang['no_available_hours']){ 
			$('#available-hours').css("margin-left", "0px");		
		} else {
			$('#available-hours').css("margin-left", "44px");
		}
		
		$('#available-hours').on("DOMSubtreeModified",function(){
			if ($("#available-hours").html() === EALang['no_available_hours']){ 
				$('#available-hours').css("margin-left", "0px");		
			} else {
				$('#available-hours').css("margin-left", "44px");
			}
		});
				
    </script>

    <?php google_analytics_script(); ?>
</body>

</html>
