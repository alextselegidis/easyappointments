<?php
/**
 * Local variables.
 *
 * @var array $groupdata
 */

$title = $groupdata['name'];

$descr = $groupdata['category']['description'];

?>
<div class="booking-category-card card h-100 rounded-3">
    <p class="card-title">
        <?= $title; ?>
    </p>
    <div class="card-body">
        <p class="card-text">
            <?= $descr ?>
        </p>
    </div>
    <div class="card-footer">
         <!-- <a href="#" class="btn rounded-pill"> <?= lang('select'); ?></a> -->
          <div class="btn rounded-pill">
            <p><?= lang('select'); ?></p>
          </div>
    </div>
</div>
