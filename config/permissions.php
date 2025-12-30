<?php

declare(strict_types=1);

return [
    'products' => [
        'permissions' => [
            [
                'key' => 'product.view',
                'description' => 'Ürünleri görüntüleme',
                'roles' => ['Admin'],
            ],
            [
                'key' => 'product.create',
                'description' => 'Ürün oluşturma',
                'roles' => ['Admin'],
            ],
            [
                'key' => 'product.edit',
                'description' => 'Ürün düzenleme',
                'roles' => ['Admin'],
            ],
            [
                'key' => 'product.deactivate',
                'description' => 'Ürün pasife alma',
                'roles' => ['Admin'],
            ],
        ],
    ],
    'caris' => [
        'permissions' => [
            [
                'key' => 'cari.view',
                'description' => 'Carileri görüntüleme',
                'roles' => ['Admin', 'Sales'],
            ],
            [
                'key' => 'cari.create',
                'description' => 'Cari oluşturma',
                'roles' => ['Admin', 'Sales'],
            ],
            [
                'key' => 'cari.edit',
                'description' => 'Cari düzenleme',
                'roles' => ['Admin', 'Sales'],
            ],
        ],
    ],
    'offers' => [
        'permissions' => [
            [
                'key' => 'offer.view',
                'description' => 'Teklifleri görüntüleme',
                'roles' => ['Admin', 'Sales'],
            ],
            [
                'key' => 'offer.create',
                'description' => 'Teklif oluşturma',
                'roles' => ['Admin', 'Sales'],
            ],
            [
                'key' => 'offer.update_status',
                'description' => 'Teklif durumu güncelleme',
                'roles' => ['Admin', 'Sales'],
            ],
        ],
    ],
    'sales' => [
        'permissions' => [
            [
                'key' => 'sale.view',
                'description' => 'Satışları görüntüleme',
                'roles' => ['Admin', 'Sales'],
            ],
            [
                'key' => 'sale.create',
                'description' => 'Satış oluşturma',
                'roles' => ['Admin', 'Sales'],
            ],
        ],
    ],
];
