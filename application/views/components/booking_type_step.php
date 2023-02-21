<?php
/**
 * Local variables.
 * 
 * @var array $available_services
 */
?>

<div id="wizard-frame-1" class="wizard-frame" style="visibility: hidden;">
    <div class="frame-container">
        <h2 class="frame-title"><?= lang('service_and_provider') ?></h2>

        <div class="row frame-content">
            <div class="col">
                <div class="mb-3">
                    <label for="select-service">
                        <strong><?= lang('service') ?></strong>
                    </label>

                    <select id="select-service" class="form-control">
                        <?php
                        // Group services by category, only if there is at least one service with a parent category.
                        $has_category = FALSE;
                        foreach ($available_services as $service)
                        {
                            if ( ! empty($service['category_id']))
                            {
                                $has_category = TRUE;
                                break;
                            }
                        }

                        if ($has_category)
                        {
                            $grouped_services = [];

                            foreach ($available_services as $service)
                            {
                                if ( ! empty($service['category_id']))
                                {
                                    if ( ! isset($grouped_services[$service['category_name']]))
                                    {
                                        $grouped_services[$service['category_name']] = [];
                                    }

                                    $grouped_services[$service['category_name']][] = $service;
                                }
                            }

                            // We need the uncategorized services at the end of the list, so we will use another
                            // iteration only for the uncategorized services.
                            $grouped_services['uncategorized'] = [];
                            foreach ($available_services as $service)
                            {
                                if ($service['category_id'] == NULL)
                                {
                                    $grouped_services['uncategorized'][] = $service;
                                }
                            }

                            foreach ($grouped_services as $key => $group)
                            {
                                $group_label = $key !== 'uncategorized'
                                    ? $group[0]['category_name']
                                    : 'Uncategorized';

                                if (count($group) > 0)
                                {
                                    echo '<optgroup label="' . $group_label . '">';
                                    foreach ($group as $service)
                                    {
                                        echo '<option value="' . $service['id'] . '">'
                                            . $service['name'] . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                            }
                        }
                        else
                        {
                            foreach ($available_services as $service)
                            {
                                echo '<option value="' . $service['id'] . '">' . $service['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="select-provider">
                        <strong><?= lang('provider') ?></strong>
                    </label>

                    <select id="select-provider" class="form-control"></select>
                </div>

                <div id="service-description"></div>
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
