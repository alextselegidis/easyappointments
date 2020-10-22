<div id="footer">
    <div id="footer-content" class="col-12 col-sm-8">
        Powered by
        <a href="https://easyappointments.org">
            Easy!Appointments

            v<?= config('version') ?>

            <?php if (config('release_label')): ?>
                - <?= config('release_label') ?>
            <?php endif ?>
        </a>

        |

        <?= lang('licensed_under') ?> GPLv3 |

        <span id="select-language" class="badge badge-secondary">
            <i class="fas fa-language mr-2"></i>
        	<?= ucfirst(config('language')) ?>
        </span>

        |

        <a href="<?= site_url('appointments') ?>">
            <?= lang('go_to_booking_page') ?>
        </a>
    </div>

    <div id="footer-user-display-name" class="col-12 col-sm-4">
        <?= lang('hello') . ', ' . $user_display_name ?>!
    </div>
</div>

<script src="<?= asset_url('assets/js/backend.js') ?>"></script>
<script src="<?= asset_url('assets/js/polyfill.js') ?>"></script>
<script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>
</body>
</html>
