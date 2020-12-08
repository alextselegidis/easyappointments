<div id="footer">
    <div id="footer-content" class="col-12 col-sm-8">
        <img class="mr-1" src="<?= base_url('assets/img/logo-16x16.png') ?>" alt="Easy!Appointments Logo">
        <a href="https://easyappointments.org">
            Easy!Appointments
        </a>

        v<?= config('version') ?>
        <?php if (config('release_label')): ?>
            - <?= config('release_label') ?>
        <?php endif ?>

        |

        <img class="mx-1" src="<?= base_url('assets/img/alextselegidis-logo-16x16.png') ?>" alt="Alex Tselegidis Logo">
        <a href="https://alextselegidis.com">
            Alex Tselegidis
        </a>
        &copy; <?= date('Y') ?> - Software Development

        |

        <?= lang('licensed_under') ?>
        <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">
            GPL-3.0
        </a>

        |

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
