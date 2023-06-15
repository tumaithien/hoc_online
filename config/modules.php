<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'frontend' => array(
        'className' => 'Learncom\Frontend\Module',
        'path' => __DIR__ . '/../apps/frontend/Module.php'
    ),
    'backend' => array(
        'className' => 'Learncom\Backend\Module',
        'path' => __DIR__ . '/../apps/backend/Module.php'
    )
));
