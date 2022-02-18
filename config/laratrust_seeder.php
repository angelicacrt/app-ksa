<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'admin' => [
            'users' => 'c,r,u,d',
            'payments' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'crew' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'logistic' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'supervisorLogisticMaster' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'supervisorLogistic' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'purchasing' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'adminPurchasing' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'purchasingManager' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'adminOperational' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'picAdmin' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'AsuransiIncident' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'picSite' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'InsuranceManager' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'adminOperational' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ]
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
