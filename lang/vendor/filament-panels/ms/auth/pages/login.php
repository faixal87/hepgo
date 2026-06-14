<?php

return [
    'actions' => [
        'request_password_reset' => [
            'label' => 'Forgot Password?',
        ],
    ],
    'form' => [
        'actions' => [
            'authenticate' => [
                'label' => 'Login',
            ],
        ],
    ],
    'multi_factor' => [
        'form' => [
            'actions' => [
                'authenticate' => [
                    'label' => 'Confirm',
                ],
            ],
        ],
    ],
];
