<?php
$capabilities = [
    'local/estimated_learning_time:learningtime' => [
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'learningtime' => CAP_ALLOW,
        ],
    ],
];
