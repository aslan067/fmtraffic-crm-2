<?php

declare(strict_types=1);

return [
    'products' => [
        'route' => '/products',
        'feature' => 'product',
        'permission' => 'product.view',
        'label' => 'Ürünler',
    ],
    'caris' => [
        'route' => '/caris',
        'feature' => 'cari',
        'permission' => 'cari.view',
        'label' => 'Cari Yönetimi',
    ],
];
