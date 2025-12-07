<?php
/**
 * Application Configuration
 * Contains general application settings
 */

return [
    'name' => 'BudgetPlanner',
    'version' => '1.0.0',
    'debug' => true,
    'timezone' => 'UTC',
    'base_url' => '',
    'session' => [
        'name' => 'budget_planner_session',
        'lifetime' => 7200, // 2 hours
    ]
];
