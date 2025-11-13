<?php
/**
 * Local variables.
 *
 * @var array $available_services
 * @var array $available_categories
 */
?>
<div id="data-island" style="visibility: hidden;">
    <input id="selectedCategory"/>
    <input id="selectedService"/>
    <input id="selectedProvider"/>
</div>
<div id="wizard-frame-1" class="wizard-frame" style="visibility: hidden;">
    <div class="frame-container">
        <h2 class="frame-title mt-md-5"><?= lang('service_and_provider') ?></h2>
        <div class="row frame-content">
            <div class="col col-lg-12">
                <div class="mb-5">
                    <?php
                        $grouped_services = [];

                        // Make sure at least the 'uncategorized' group exists
                        $grouped_services[ 'uncategorized' ]['services'] = [];

                        // Group all services in categories
                        foreach ($available_services as $service) {
						$group_name = empty( $service['service_category_id'] )
							? 'uncategorized'
							: $available_categories[ $service['service_category_id'] ]['name'];

                            if (!isset($grouped_services[$group_name])) {
                                $grouped_services[ $group_name ]['services'] = [];
							    $grouped_services[ $group_name ]['category'] = 
                                        $available_categories[ $service['service_category_id'] ];
                            }

                            $grouped_services[ $group_name ]['services'][] = $service;
                        }

                        // Check if there are categoriesother then 'uncategorized'
                        if (count($grouped_services) > 1) 
                        {
						    ?>
                                <div class="categories-group entity-group container">
                                <span class="booking-group-title"> <?= lang('categories'); ?></span>
                                <div class="row justify-content-left row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                            <?php
                            foreach(array_keys($grouped_services) as $key) 
                            {
                                if ( $key !== 'uncategorized' ) 
                                {
								    echo '<div class="col">';
                                    $groupdata['name'] = $key;
                                    $groupdata['services'] = $grouped_services[ $key ]['services'];
                                    $groupdata['category'] = $grouped_services[ $key ]['category'];

                                    component(component: 'booking_category_card', vars: ['groupdata' => $groupdata]);

                                    echo '</div>';
                                }
                            }
						    echo '</div></div>';
                        }

					foreach ( $grouped_services as $group ) {
                        $name = lang( 'services' );
						$hidden = '';
						$groupid = '0';

                        if (isset($group['category'])) {
							$name = $group['category']['name'];
							$hidden = 'display-none';
                            $groupid = $group['category']['id'];
                        }
						

                    ?>
                            <div class="services-group entity-group container groupid-<?= $groupid; ?> <?= $hidden; ?>">
                    
                            <p> 
                                <span class="btn btn-back rounded-pill display-none"><i class="fas fa-angles-left"></i></span>
                                <span class="booking-group-title"><?= $name; ?></span>
                            </p>
                    
                            <div class="row justify-content-left row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                    <?php

                        // ... and show them
                        foreach ( $group['services'] as $service ) {
                            echo '<div class="col">';
                            component( 'booking_service_card', [ 'service' => $service ] );
                            echo '</div>';
                        }
                        echo '</div></div>';
                
					}
                    ?>
                </div>

                <div class="mb-3" hidden>
                    <label for="select-subservices">
                        <strong><?= lang('subservice') ?></strong>
                    </label>
                    <select id="select-subservices" class="form-select">
                        <option value="">
                            <?= lang('please_select') ?>
                        </option>
                    </select>
                </div>

                <div class="mb-3" hidden>
                    <label for="select-provider">
                        <strong><?= lang('provider') ?></strong>
                    </label>

                    <select id="select-provider" class="form-select">
                        <option value="">
                            <?= lang('please_select') ?>
                        </option>
                    </select>
                </div>

                <div id="service-description" class="small">
                    <!-- JS -->
                </div>
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