<?php
/**
 * Booking No Services Message Component
 *
 * Displays an info message when no services or providers are available for booking.
 */
?>
<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3"><?= lang('no_available_service_providers') ?></h3>
                        <p class="text-muted mb-0">
                            <?= lang('no_available_service_providers_message') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
