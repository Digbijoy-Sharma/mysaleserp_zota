<?php

/**
 * Dava India — Multi-Store Pharmacy Configuration
 *
 * This config centralises all feature flags and tunables for the
 * Dava India (2800-store pharmacy chain) deployment. Every check
 * in the codebase should read from here instead of hard-coding.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Master switch
    |--------------------------------------------------------------------------
    | When false, the app behaves exactly like the original UltimatePOS
    | multi-tenant setup. When true, Dava India mode is active:
    |  - Each business row = one store
    |  - 1 business_location auto-created per business (hidden from UI)
    |  - Central product master, central vendor master enabled
    |  - Super admin can manage everything cross-store
    */
    'enabled' => env('DAVA_INDIA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Sub-features
    |--------------------------------------------------------------------------
    */
    'central_catalog' => [
        'enabled' => env('DAVA_CENTRAL_CATALOG_ENABLED', true),
        // Default to making new central products available in every store
        'auto_assign_all_stores' => env('DAVA_CENTRAL_AUTO_ASSIGN', true),
        // Cache the central product list for N seconds
        'cache_ttl' => env('DAVA_CENTRAL_CACHE_TTL', 300),
    ],

    'central_vendors' => [
        'enabled' => env('DAVA_CENTRAL_VENDORS_ENABLED', true),
        'auto_assign_all_stores' => env('DAVA_CENTRAL_VENDORS_AUTO_ASSIGN', true),
    ],

    'multi_location' => [
        // When true: each business can have many business_locations (classic mode)
        // When false: each business is forced to have exactly 1 business_location
        //             auto-created with the same name; UI hides location management
        'enabled' => env('DAVA_MULTI_LOCATION_ENABLED', false),
    ],

    'superadmin' => [
        // The Spatie permission that grants super-admin bypass
        'permission' => 'superadmin.access',
        // If true, super admins also bypass the per-business_id scoping in
        // every query (most useful for cross-store reports)
        'bypass_business_scope' => env('DAVA_SUPERADMIN_BYPASS_SCOPE', true),
    ],

    'pharmacy' => [
        // Enforce FEFO (First Expiry First Out) at POS
        'fefo' => env('DAVA_PHARMACY_FEFO', true),
        // Warn at POS for prescription-required / scheduled drugs
        'pos_schedule_warning' => env('DAVA_PHARMACY_POS_WARNING', true),
        // Block sale of expired batch
        'block_expired_sale' => env('DAVA_PHARMACY_BLOCK_EXPIRED', true),
    ],

    'reports' => [
        // Email digest of low-stock items to super admins
        'stock_alert_digest' => env('DAVA_STOCK_ALERT_DIGEST', true),
        'stock_alert_digest_time' => env('DAVA_STOCK_ALERT_DIGEST_TIME', '09:00'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Super Admin (seeded on first run)
    |--------------------------------------------------------------------------
    */
    'default_superadmin' => [
        'username' => env('DAVA_SUPERADMIN_USERNAME', 'davaadmin'),
        'email' => env('DAVA_SUPERADMIN_EMAIL', 'admin@davaindia.local'),
        'password' => env('DAVA_SUPERADMIN_PASSWORD', 'Dava@SuperAdmin#2026'),
        'first_name' => env('DAVA_SUPERADMIN_FIRST_NAME', 'Dava'),
        'last_name' => env('DAVA_SUPERADMIN_LAST_NAME', 'Super Admin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules
    |--------------------------------------------------------------------------
    | The set of modules kept ON for Dava India. Everything else is turned
    | off in modules_statuses.json.
    */
    'enabled_modules' => [
        'Superadmin',
        'Essentials',
        'Accounting',
        'AssetManagement',
        'Connector',
    ],
];
