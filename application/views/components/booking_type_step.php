<?php
/**
 * Local variables.
 *
 * @var array $available_services
 * @var array $available_providers
 * @var string $service_selection_layout
 * @var bool|string $hide_single_provider_selection
 */

$service_selection_layout = $service_selection_layout ?? 'dropdown';
$use_service_accordion = $service_selection_layout === 'accordion';
$hide_single_provider_selection = filter_var($hide_single_provider_selection ?? false, FILTER_VALIDATE_BOOLEAN);
$should_show_select_service_title = $hide_single_provider_selection && count($available_providers ?? []) === 1;

$grouped_services = [];

foreach ($available_services as $service) {
    $category_name = !empty($service['service_category_id']) ? $service['service_category_name'] : lang('uncategorized');

    if (!isset($grouped_services[$category_name])) {
        $grouped_services[$category_name] = [];
    }

    $grouped_services[$category_name][] = $service;
}
?>

<div id="wizard-frame-1" class="wizard-frame p-3 p-md-4" style="visibility: hidden;">
    <div class="frame-container py-3" style="min-height: 500px;">
        <h2 class="frame-title fw-light text-center mb-4 text-muted mt-md-5">
            <?= $should_show_select_service_title ? lang('select_service') : lang('service_and_provider') ?>
        </h2>

        <div class="row frame-content">
            <div class="col col-lg-8 offset-md-2">
                <div class="mb-3 <?= $use_service_accordion ? 'visually-hidden' : '' ?>">
                    <label for="select-service" class="fs-5 mb-2">
                        <strong><?= lang('service') ?></strong>
                    </label>

                    <select id="select-service" class="form-select mb-4">
                        <option value="">
                            <?= lang('please_select') ?>
                        </option>
                        <?php
                        // Group services by category, only if there is at least one service with a parent category.
                        $has_category = false;
                        foreach ($available_services as $service) {
                            if (!empty($service['service_category_id'])) {
                                $has_category = true;
                                break;
                            }
                        }

                        if ($has_category) {
                            $select_grouped_services = [];

                            foreach ($available_services as $service) {
                                if (!empty($service['service_category_id'])) {
                                    if (!isset($select_grouped_services[$service['service_category_name']])) {
                                        $select_grouped_services[$service['service_category_name']] = [];
                                    }

                                    $select_grouped_services[$service['service_category_name']][] = $service;
                                }
                            }

                            // We need the uncategorized services at the end of the list, so we will use another
                            // iteration only for the uncategorized services.
                            $select_grouped_services['uncategorized'] = [];
                            foreach ($available_services as $service) {
                                if ($service['service_category_id'] == null) {
                                    $select_grouped_services['uncategorized'][] = $service;
                                }
                            }

                            foreach ($select_grouped_services as $key => $group) {
                                $group_label =
                                    $key !== 'uncategorized' ? $group[0]['service_category_name'] : 'Uncategorized';

                                if (count($group) > 0) {
                                    echo '<optgroup label="' . e($group_label) . '">';
                                    foreach ($group as $service) {
                                        echo '<option value="' .
                                            $service['id'] .
                                            '">' .
                                            e($service['name']) .
                                            '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                            }
                        } else {
                            foreach ($available_services as $service) {
                                echo '<option value="' . $service['id'] . '">' . e($service['name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <?php if ($use_service_accordion): ?>
                    <div id="service-selection-accordion" class="service-selection-accordion mb-4">
                        <?php foreach ($grouped_services as $category_name => $services): ?>
                            <?php if (empty($services)): ?>
                                <?php continue; ?>
                            <?php endif; ?>

                            <?php $category_id = 'service-category-' . md5($category_name); ?>

                            <section class="service-category-panel mb-2">
                                <button type="button"
                                        class="service-category-toggle"
                                        aria-expanded="false"
                                        aria-controls="<?= e($category_id) ?>">
                                    <span><?= e($category_name) ?> (<?= count($services) ?>)</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>

                                <div id="<?= e($category_id) ?>" class="service-category-services" hidden>
                                    <?php foreach ($services as $service): ?>
                                        <button type="button"
                                                class="service-accordion-option"
                                                data-service-id="<?= e($service['id']) ?>">
                                            <span class="service-option-main">
                                                <span class="service-option-name"><?= e($service['name']) ?></span>

                                                <span class="service-option-meta">
                                                    <?php if (!empty($service['duration'])): ?>
                                                        <?= e($service['duration']) ?> <?= lang('minutes') ?>
                                                    <?php endif; ?>
                                                </span>
                                            </span>

                                            <span class="service-option-side">
                                                <?php if ((float) ($service['price'] ?? 0) > 0): ?>
                                                    <span class="service-option-price">
                                                        <?= e(number_format((float) $service['price'], 2)) ?> <?= e($service['currency'] ?? '') ?>
                                                    </span>
                                                <?php endif; ?>

                                                <span class="service-option-select"><?= lang('select') ?></span>
                                            </span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-3" hidden>
                    <label for="select-provider" class="fs-5 mb-2">
                        <strong><?= lang('provider') ?></strong>
                    </label>

                    <select id="select-provider" class="form-select mb-4">
                        <option value="">
                            <?= lang('please_select') ?>
                        </option>
                    </select>
                </div>

                <div id="service-description" class="small overflow-auto shadow-none" style="max-height: 153px;">
                    <!-- JS -->
                </div>

            </div>
        </div>
    </div>

    <div class="command-buttons text-center my-3 mx-auto d-md-flex justify-content-md-between">
        <span>&nbsp;</span>

        <button type="button" id="button-next-1" class="btn button-next btn-dark" style="min-width: 120px; margin-right: 10px;"
                data-step_index="1">
            <?= lang('next') ?>
            <i class="fas fa-chevron-right ms-2"></i>
        </button>
    </div>
</div>
