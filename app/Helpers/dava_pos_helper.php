<?php
/**
 * Dava India — POS Helper File
 *
 * Loaded automatically on every request when DAVA_INDIA_ENABLED=true
 * (registered in composer.json autoload "files" array).
 *
 * Provides tiny per-feature toggles that the existing UltimatePOS
 * controllers can check via `function_exists('dava_pos_*')` or by
 * reading `config('dava.*')`.
 */

if (! defined('DAVA_POS_HELPER_LOADED')) {
    define('DAVA_POS_HELPER_LOADED', true);

    /**
     * Should the POS enforce First-Expiry-First-Out batch picking?
     */
    function dava_pos_use_fefo(): bool
    {
        if (! function_exists('config')) {
            return false;
        }
        return (bool) config('dava.pharmacy.fefo', false);
    }

    /**
     * Should the POS warn when a prescription-required drug (Rx) is
     * added to the cart without a prescription number on the sale?
     */
    function dava_pos_warn_prescription(): bool
    {
        if (! function_exists('config')) {
            return false;
        }
        return (bool) config('dava.pharmacy.pos_schedule_warning', true);
    }

    /**
     * Should the POS hard-block sale of expired batches?
     */
    function dava_pos_block_expired(): bool
    {
        if (! function_exists('config')) {
            return false;
        }
        return (bool) config('dava.pharmacy.block_expired_sale', true);
    }

    /**
     * Should the POS show the drug schedule (H/H1/X/OTC) badge on
     * each line in the cart?
     */
    function dava_pos_show_schedule_badge(): bool
    {
        if (! function_exists('config')) {
            return false;
        }
        return (bool) config('dava.pharmacy.pos_schedule_warning', true);
    }

    /**
     * Is Dava India mode currently enabled?
     */
    function dava_india_enabled(): bool
    {
        if (! function_exists('config')) {
            return false;
        }
        return (bool) config('dava.enabled', false);
    }

    /**
     * Is the current user a Dava India super admin?
     */
    function dava_is_superadmin(): bool
    {
        if (! function_exists('auth') || ! auth()->check()) {
            return false;
        }
        $u = auth()->user();
        return method_exists($u, 'isSuperadmin') && $u->isSuperadmin();
    }

    /**
     * For a given variation, return the FEFO batch plan from the
     * DavaFefoService. Thin convenience wrapper so callers don't have
     * to import the service class.
     */
    function dava_pos_fefo_plan(int $business_id, int $location_id, int $variation_id, float $quantity): array
    {
        return app(\App\Services\DavaFefoService::class)
            ->planFefoConsumption($business_id, $location_id, $variation_id, $quantity);
    }
}
