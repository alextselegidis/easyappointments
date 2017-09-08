<script src="<?php echo base_url('assets/js/backend_settings_system.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/backend_settings_user.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/backend_settings.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/working_plan.js'); ?>"></script>
<script src="<?php echo base_url('assets/ext/jquery-ui/jquery-ui-timepicker-addon.js'); ?>"></script>
<script src="<?php echo base_url('assets/ext/jquery-jeditable/jquery.jeditable.min.js'); ?>"></script>
<script>
    var GlobalVariables = {
        'csrfToken'     : <?php echo json_encode($this->security->get_csrf_hash()); ?>,
        'baseUrl'       : <?php echo json_encode($base_url); ?>,
        'dateFormat'    : <?php echo json_encode($date_format); ?>,
        'userSlug'      : <?php echo json_encode($role_slug); ?>,
        'settings'      : {
            'system'    : <?php echo json_encode($system_settings); ?>,
            'user'      : <?php echo json_encode($user_settings); ?>
        },
        'user'          : {
            'id'        : <?php echo $user_id; ?>,
            'email'     : <?php echo json_encode($user_email); ?>,
            'role_slug' : <?php echo json_encode($role_slug); ?>,
            'privileges': <?php echo json_encode($privileges); ?>
        }
    };

    $(document).ready(function() {
        BackendSettings.initialize(true);
    });
</script>

<div id="settings-page" class="container-fluid">
    <ul class="nav nav-tabs">
        <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE): ?>
            <li role="representation" class="general-tab tab"><a><?php echo lang('general'); ?></a></li>
        <?php endif ?>

        <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE): ?>
            <li role="representation" class="business-logic-tab tab"><a><?php echo lang('business_logic'); ?></a></li>
        <?php endif ?>

        <?php if ($privileges[PRIV_USER_SETTINGS]['view'] == TRUE): ?>
            <li role="representation" class="user-tab tab"><a><?php echo lang('current_user'); ?></a></li>
        <?php endif ?>

        <li role="representation" class="about-tab tab"><a><?php echo lang('about_ea'); ?></a></li>
    </ul>

    <!-- GENERAL TAB -->

    <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="general" class="tab-content <?php echo $hidden; ?>">
        <form>
            <fieldset>
                <legend>
                    <?php echo lang('general_settings'); ?>
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE): ?>
                    <button type="button" class="save-settings btn btn-primary btn-xs"
                            title="<?php echo lang('save'); ?>">
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                        <?php echo lang('save'); ?>
                    </button>
                    <?php endif ?>
                </legend>

                <div class="wrapper row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company-name"><?php echo lang('company_name'); ?> *</label>
                            <input type="text" id="company-name" data-field="company_name" class="required form-control">
                            <span class="help-block">
                                <?php echo lang('company_name_hint'); ?>
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="company-email"><?php echo lang('company_email'); ?> *</label>
                            <input type="text" id="company-email" data-field="company_email" class="required form-control">
                            <span class="help-block">
                                <?php echo lang('company_email_hint'); ?>
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="company-link"><?php echo lang('company_link'); ?> *</label>
                            <input type="text" id="company-link" data-field="company_link" class="required form-control">
                            <span class="help-block">
                                <?php echo lang('company_link_hint'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="google-analytics-code">
                                Google Analytics ID</label>
                            <input type="text" id="google-analytics-code" placeholder="UA-XXXXXXXX-X"
                                data-field="google_analytics_code" class="form-control">
                            <span class="help-block">
                                <?php echo lang('google_analytics_code_hint'); ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="date-format">
                                <?php echo lang('date_format'); ?>
                            </label>
                            <select class="form-control" id="date-format" data-field="date_format">
                                <option value="DMY">DMY</option>
                                <option value="MDY">MDY</option>
                                <option value="YMD">YMD</option>
                            </select>
                            <span class="help-block">
                                <?php echo lang('date_format_hint'); ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <label><?php echo lang('customer_notifications'); ?></label>
                            <br>
                            <button type="button" id="customer-notifications" class="btn btn-default" data-toggle="button" aria-pressed="false">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <?php echo lang('receive_notifications'); ?>
                            </button>
                            <span class="help-block">
                                <?php echo lang('customer_notifications_hint'); ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="require-captcha">
                                CAPTCHA
                            </label>
                            <br>
                            <button type="button" id="require-captcha" class="btn btn-default" data-toggle="button" aria-pressed="false">
                                <span class="glyphicon glyphicon-lock"></span>
                                <?php echo lang('require_captcha'); ?>
                            </button>
                            <span class="help-block">
                                <?php echo lang('require_captcha_hint'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <!-- BUSINESS LOGIC TAB -->

    <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="business-logic" class="tab-content <?php echo $hidden; ?>">
        <form>
            <fieldset>
                <legend>
                    <?php echo lang('business_logic'); ?>
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE): ?>
                    <button type="button" class="save-settings btn btn-primary btn-xs"
                            title="<?php echo lang('save'); ?>">
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                        <?php echo lang('save'); ?>
                    </button>
                    <?php endif ?>
                </legend>

                <div class="row">
                    <div class="col-md-7 working-plan-wrapper">
                        <h4><?php echo lang('working_plan'); ?></h4>
                        <span class="help-block">
                            <?php echo lang('edit_working_plan_hint'); ?>
                        </span>

                        <table class="working-plan table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo lang('day'); ?></th>
                                    <th><?php echo lang('start'); ?></th>
                                    <th><?php echo lang('end'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="monday" />
                                                    <?php echo lang('monday'); ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td><input type="text" id="monday-start" class="work-start" /></td>
                                    <td><input type="text" id="monday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="tuesday" />
                                                    <?php echo lang('tuesday'); ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td><input type="text" id="tuesday-start" class="work-start" /></td>
                                    <td><input type="text" id="tuesday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="wednesday" />
                                                    <?php echo lang('wednesday'); ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td><input type="text" id="wednesday-start" class="work-start" /></td>
                                    <td><input type="text" id="wednesday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="thursday" />
                                                    <?php echo lang('thursday'); ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td><input type="text" id="thursday-start" class="work-start" /></td>
                                    <td><input type="text" id="thursday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="friday" />
                                                    <?php echo lang('friday'); ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td><input type="text" id="friday-start" class="work-start" /></td>
                                    <td><input type="text" id="friday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="saturday" />
                                                    <?php echo lang('saturday'); ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td><input type="text" id="saturday-start" class="work-start" /></td>
                                    <td><input type="text" id="saturday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="sunday" />
                                                    <?php echo lang('sunday'); ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td><input type="text" id="sunday-start" class="work-start" /></td>
                                    <td><input type="text" id="sunday-end" class="work-end" /></td>
                                </tr>
                            </tbody>
                        </table>

                        <br>

                        <h4><?php echo lang('book_advance_timeout'); ?></h4>
                        <span class="help-block">
                            <?php echo lang('book_advance_timeout_hint'); ?>
                        </span>
                        <div class="form-group">
                            <label for="book-advance-timeout"><?php echo lang('timeout_minutes'); ?></label>
                            <input type="text" id="book-advance-timeout" data-field="book_advance_timeout" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-5 breaks-wrapper">
                        <h4><?php echo lang('breaks'); ?></h4>

                        <span class="help-block">
                            <?php echo lang('edit_breaks_hint'); ?>
                        </span>

                        <div>
                            <button type="button" class="add-break btn btn-primary">
                                <span class="glyphicon glyphicon-white glyphicon glyphicon-plus"></span>
                                <?php echo lang('add_break');?>
                            </button>
                        </div>

                        <br>

                        <table class="breaks table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo lang('day'); ?></th>
                                    <th><?php echo lang('start'); ?></th>
                                    <th><?php echo lang('end'); ?></th>
                                    <th><?php echo lang('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <!-- USER TAB -->

    <?php $hidden = ($privileges[PRIV_USER_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="user" class="tab-content <?php echo $hidden; ?>">
        <form class="row">
            <fieldset class="col-xs-12 col-sm-6 personal-info-wrapper">
                <legend>
                    <?php echo lang('personal_information'); ?>
                    <?php if ($privileges[PRIV_USER_SETTINGS]['edit'] == TRUE): ?>
                    <button type="button" class="save-settings btn btn-primary btn-xs"
                            title="<?php echo lang('save'); ?>">
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                        <?php echo lang('save'); ?>
                    </button>
                    <?php endif; ?>
                </legend>

                <input type="hidden" id="user-id" />

                <div class="form-group">
                    <label for="first-name"><?php echo lang('first_name'); ?> *</label>
                    <input type="text" id="first-name" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="last-name"><?php echo lang('last_name'); ?> *</label>
                    <input type="text" id="last-name" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="email"><?php echo lang('email'); ?> *</label>
                    <input type="text" id="email" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="phone-number"><?php echo lang('phone_number'); ?> *</label>
                    <input type="text" id="phone-number" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="mobile-number"><?php echo lang('mobile_number'); ?></label>
                    <input type="text" id="mobile-number" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="address"><?php echo lang('address'); ?></label>
                    <input type="text" id="address" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="city"><?php echo lang('city'); ?></label>
                    <input type="text" id="city" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="state"><?php echo lang('state'); ?></label>
                    <input type="text" id="state" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="zip-code"><?php echo lang('zip_code'); ?></label>
                    <input type="text" id="zip-code" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="notes"><?php echo lang('notes'); ?></label>
                    <textarea id="notes" class="form-control" rows="3"></textarea>
                </div>
            </fieldset>

            <fieldset class="col-xs-12 col-sm-6 miscellaneous-wrapper">
                <legend><?php echo lang('system_login'); ?></legend>

                <div class="form-group">
                    <label for="username"><?php echo lang('username'); ?> *</label>
                    <input type="text" id="username" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="password"><?php echo lang('password'); ?></label>
                    <input type="password" id="password" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="retype-password"><?php echo lang('retype_password'); ?></label>
                    <input type="password" id="retype-password" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="calendar-view"><?php echo lang('calendar'); ?> *</label>
                    <select id="calendar-view" class="form-control required">
                        <option value="default">Default</option>
                        <option value="table">Table</option>
                    </select>
                </div>

                <button type="button" id="user-notifications" class="btn btn-default" data-toggle="button">
                    <span class="glyphicon glyphicon-envelope"></span>
                    <?php echo lang('receive_notifications'); ?>
                </button>
            </fieldset>
        </form>
    </div>

    <!-- ABOUT TAB -->

    <div id="about" class="tab-content">
        <h3>Easy!Appointments</h3>
        <p>
            <?php echo lang('about_ea_info'); ?>
        </p>

        <br>

        <div class="current-version">
            <?php
                echo lang('current_version') . ' ';
                echo $this->config->item('version');
                $release_title = $this->config->item('release_label');
                if ($release_title != '') {
                    echo ' - ' . $release_title;
                }
            ?>
        </div>

		<br>

        <h3><?php echo lang('support'); ?></h3>
        <p>
            <?php echo lang('about_ea_support'); ?>
            <br><br>
            <a href="http://easyappointments.org">
                <?php echo lang('official_website'); ?>
            </a>
            |
            <a href="https://groups.google.com/forum/#!forum/easy-appointments">
                <?php echo lang('support_group'); ?>
            </a>
            |
            <a href="https://github.com/alextselegidis/easyappointments/issues">
                <?php echo lang('project_issues'); ?>
            </a>
            |
            <a href="http://easyappointments.wordpress.com">
                E!A Blog
            </a>
            |
            <a href="https://www.facebook.com/easyappointments.org">
                Facebook
            </a>
            |
            <a href="https://plus.google.com/+EasyappointmentsOrg">
                Google+
            </a>
            |
            <a href="https://twitter.com/EasyAppts">
                Twitter
            </a>
            |
            <a href="https://plus.google.com/communities/105333709485142846840">
                <?php echo lang('google_plus_community'); ?>
            </a>
        </p>

		<br>

		<h3><?php echo lang('license'); ?></h3>
		<p>
            <?php echo lang('about_ea_license'); ?>
            <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
        </p>
    </div>
</div>
