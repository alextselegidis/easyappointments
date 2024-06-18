<?php
/**
 * Local variables.
 *
 * @var array $available_services
 */
?>

<div id="wizard-frame-1" class="wizard-frame" style="visibility: hidden;">
    <div class="frame-container">
        <h2 class="frame-title mt-md-5"><?= lang('service_and_provider') ?></h2>

        <div class="row frame-content">
            <div class="col col-md-8 offset-md-2">
                <div class="mb-3">
                    <label for="select-service">
                        <strong><?= lang('service') ?></strong>
                    </label>

                    <select id="select-service" class="form-select">
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
                            $grouped_services = [];

                            foreach ($available_services as $service) {
                                if (!empty($service['service_category_id'])) {
                                    if (!isset($grouped_services[$service['service_category_name']])) {
                                        $grouped_services[$service['service_category_name']] = [];
                                    }

                                    $grouped_services[$service['service_category_name']][] = $service;
                                }
                            }

                            // We need the uncategorized services at the end of the list, so we will use another
                            // iteration only for the uncategorized services.
                            $grouped_services['uncategorized'] = [];
                            foreach ($available_services as $service) {
                                if ($service['service_category_id'] == null) {
                                    $grouped_services['uncategorized'][] = $service;
                                }
                            }

                            foreach ($grouped_services as $key => $group) {
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
                
                <?php slot('after_select_service'); ?>

                <div class="mb-3">
                    <label for="select-provider">
                        <strong><?= lang('provider') ?></strong>
                    </label>

                    <select id="select-provider" class="form-select">
                        <option value="">
                            <?= lang('please_select') ?>
                        </option>
                    </select>
                </div>

                <?php slot('after_select_provider'); ?>

                <div id="service-description" class="small">
                    <!-- JS -->
                </div>

                <?php slot('after_service_description'); ?>
                
            </div>
        </div>
    </div>

    <div class="command-buttons">
        <span>&nbsp;</span>

        <button type="button" id="button-next-1" class="btn button-next btn-dark"
                data-step_index="1">
            <?= lang('next') ?>
            <i class="fas fa-chevron-right ms-2"></i>
        </button>
    </div>
</div>
