<?php
/**
 * Local variables.
 *
 * @var array $service
 */

$title = $service['name'];

$descr = $service['description'];

$duration = $service['duration'];

$price = number_format( num: 0.0 + $service['price'], decimals: 2,
	decimal_separator: ',', thousands_separator: '.' );

?>

<div class="booking-service-card card h-100 rounded-3">
    <p class="card-title">
            <?= $title; ?>
        </p>
        
    <div class="card-body">
        <p class="card-text">
            <?= $descr ?>
        </p>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col">
                <div class="btn rounded-pill">
                <p data="<?= $service['id']; ?>"> <?= lang('select'); ?></p>
                </div>
            </div>
            <div class="col">
                <p>€ <?= $price; ?>,  <?= $duration ?> min.</p>
            </div>
         </div>
    </div>
</div>