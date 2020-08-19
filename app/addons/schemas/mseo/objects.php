<?php

/**
 * Get the sharing of things per dispatch
 */
$schema = [
    'products.view' => [
        'is_shared' => function () {
            return call_user_func_array('fn_ult_is_shared_object', func_get_args());
        },
        'identifier' => 'product_id',
        'type' => 'products'
    ],
    'pages.view' => [
        'is_shared' => function () {
            return call_user_func_array('fn_ult_is_shared_object', func_get_args());
        },
        'identifier' => 'page_id',
        'type' => 'pages'
    ],
    'index.index' => [
        'is_shared' => function () {
            return true;
        },
        'persistent' => true
    ]
];

return $schema;