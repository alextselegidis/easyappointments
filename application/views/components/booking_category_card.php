<?php
/**
 * Local variables.
 *
 * @var array $groupdata
 */

$title = $groupdata['name'];

$descr = $groupdata['category']['description'];

$groupid = $groupdata['category']['id'];

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
            <p data="<?= $groupid; ?>"><?= lang('select'); ?></p>
          </div>
    </div>
</div>
