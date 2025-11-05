<?php
/**
 * Local variables.
 *
 * @var array $available_services
 * @var array $available_categories
 */
?>

<div id="wizard-frame-1" class="wizard-frame" style="visibility: hidden;">
    <div class="frame-container">
        <h2 class="frame-title mt-md-5"><?= lang('service_and_provider') ?></h2>
        <div class="row frame-content">
            <div class="col col-lg-12">
                <div class="mb-5">
                    <?php
                        $grouped_services = [];

                        // Make sure at least the 'uncategorized' group exists
                        $grouped_services[ 'uncategorized' ] = [];

                        // Group all services in categories
                        foreach ($available_services as $service) {
                            $group_name = empty( $service['service_category_id'] )
                                ? 'uncategorized'
                                : $service['service_category_name'];

                            if (!isset($grouped_services[$group_name])) {
                                $grouped_services[ $group_name ] = [];
                            }

                            $grouped_services[ $group_name ][] = $service;
                        }

                        // Check if there are categoriesother then 'uncategorized'
                        if (count($grouped_services) > 1) 
                        {
						    ?>
                                <div class="categories-group container">
                                <p class="booking-group-title"> <?= lang('categories'); ?></p>
                                <div class="row justify-content-left row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                            <?php
                            foreach(array_keys($grouped_services) as $key) 
                            {
                                if ( $key !== 'uncategorized' ) 
                                {
								    echo '<div class="col">';
                                    $groupdata['name'] = $key;
                                    $groupdata['services'] = $grouped_services[ $key ];
									$groupdata['category'] = $available_categories[ $grouped_services[ $key ][0]['service_category_id'] ];

                                    component(component: 'booking_category_card', vars: ['groupdata' => $groupdata]);

                                    echo '</div>';
                                }
                            }
						    echo '</div></div>';
                        }

                        // Check if there are any uncategorized services
                        if (count($grouped_services['uncategorized']) > 0) 
                        {
                            ?>
                                <div class="categories-group container">
                                <p class="booking-group-title"> <?= lang('services'); ?></p>
                                <div class="row justify-content-left row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                            <?php

                            // ... and show them
                            foreach($grouped_services['uncategorized'] as $service) 
                            {
                                echo '<div class="col">';
                                component( 'booking_service_card', ['service' => $service ] );
								echo '</div>';        
                            }
                            echo '</div></div>';
                        }

                        ?>
                </div>
            </div>
        </div>

    </div>
</div>