<?php

return [
    'roles' => [
        'super_admin' => 'Pentadbir Utama',
        'hep_admin' => 'Admin HEP',
        'hep_staff' => 'Staf HEP',
        'owner' => 'Pemilik Rumah',
        'student' => 'Pelajar',
        'parent' => 'Ibu Bapa / Penjaga',
        'api_client' => 'Klien API',
    ],

    'admin_panel_roles' => [
        'super_admin',
        'hep_admin',
        'hep_staff',
    ],

    'public_registration_roles' => [
        'owner',
        'student',
        'parent',
    ],

    'permissions' => [
        'view dashboard',
        'view users',
        'create users',
        'edit users',
        'delete users',
        'manage roles',
        'manage permissions',
        'access api',
        'view owners',
        'create owners',
        'edit owners',
        'delete owners',
        'verify owners',
        'view properties',
        'create properties',
        'edit properties',
        'delete properties',
        'verify properties',
        'update property availability',
        'view areas',
        'create areas',
        'edit areas',
        'delete areas',
        'view categories',
        'create categories',
        'edit categories',
        'delete categories',
        'view facilities',
        'create facilities',
        'edit facilities',
        'delete facilities',
    ],
];
