<?php

return [
    'items' => [
        [
            'key' => 'dashboard',
            'name' => 'Dashboard',
            'icon' => 'fas fa-chart-line',
            'description' => 'Main dashboard and overview',
        ],
        [
            'key' => 'user_management',
            'name' => 'User Management',
            'icon' => 'fas fa-users',
            'description' => 'Manage users and access control',
        ],
        [
            'key' => 'customer',
            'name' => 'Customer',
            'icon' => 'fas fa-user-circle',
            'description' => 'Manage customer data',
        ],
        [
            'key' => 'contract',
            'name' => 'Contract',
            'icon' => 'fas fa-building',
            'description' => 'Manage contract data',
        ],
        [
            'key' => 'area',
            'name' => 'Area',
            'icon' => 'fas fa-globe',
            'description' => 'Manage area data',
        ],
        [
            'key' => 'invoice',
            'name' => 'Invoice',
            'icon' => 'fas fa-project-diagram',
            'description' => 'Manage invoice data',
        ],
        [
            'key' => 'vessel',
            'name' => 'Vessel',
            'icon' => 'fas fa-ship',
            'description' => 'Manage vessel data',
        ],
        [
            'key' => 'port',
            'name' => 'Port',
            'icon' => 'fas fa-anchor',
            'description' => 'Manage port data',
        ],
        [
            'key' => 'other',
            'name' => 'Other',
            'icon' => 'fas fa-ellipsis-h',
            'description' => 'Manage other data',
        ],


        // Management Data Menu
        [
            'key' => 'jo_contract',
            'name' => 'JO Contract',
            'icon' => 'fas fa-pen-fancy',
            'description' => 'Manage job order contract data',
        ],
    ],

    'permissions' => [
        'monitor' => 'Monitor',
        'view_detail' => 'View Detail',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'download' => 'Download',
    ],
];