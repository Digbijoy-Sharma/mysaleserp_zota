<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

/**
 * Dava India — Phase 6 / Item 7
 *
 * Runtime POS settings. Reads/writes the .env file directly so Super
 * Admin can flip FEFO / block-expired / schedule-warning flags without
 * a code deploy. After save, we blow the config cache so the change
 * takes effect on the very next request.
 */
class PosSettingsController extends Controller
{
    public function index()
    {
        $flags = $this->loadFlags();
        return view('superadmin.settings.pos', ['flags' => $flags]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'DAVA_PHARMACY_FEFO'           => 'nullable|boolean',
            'DAVA_PHARMACY_POS_WARNING'    => 'nullable|boolean',
            'DAVA_PHARMACY_BLOCK_EXPIRED'  => 'nullable|boolean',
            'DAVA_CENTRAL_AUTO_ASSIGN'     => 'nullable|boolean',
            'DAVA_STOCK_ALERT_DIGEST'      => 'nullable|boolean',
        ]);

        $this->writeEnv($data);
        $this->flushCaches();

        return redirect()->route('super.settings.pos')
            ->with('status', ['success' => 1, 'msg' => 'POS settings updated.']);
    }

    /**
     * Read the live values from config (which reads from .env).
     */
    protected function loadFlags(): array
    {
        return [
            'DAVA_PHARMACY_FEFO'          => (bool) config('dava.pharmacy.fefo', false),
            'DAVA_PHARMACY_POS_WARNING'   => (bool) config('dava.pharmacy.pos_schedule_warning', true),
            'DAVA_PHARMACY_BLOCK_EXPIRED' => (bool) config('dava.pharmacy.block_expired_sale', true),
            'DAVA_CENTRAL_AUTO_ASSIGN'    => (bool) config('dava.central_catalog.auto_assign_all_stores', true),
            'DAVA_STOCK_ALERT_DIGEST'     => (bool) config('dava.reports.stock_alert_digest', true),
        ];
    }

    /**
     * Update the .env file. Only writes the keys we manage; everything
     * else in .env is preserved as-is.
     */
    protected function writeEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            return;
        }
        $contents = File::get($envPath);

        foreach ($data as $key => $value) {
            $bool = $value ? 'true' : 'false';
            $pattern = "/^{$key}=.*$/m";
            $line    = "{$key}={$bool}";
            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, $line, $contents);
            } else {
                $contents .= PHP_EOL . $line;
            }
        }

        File::put($envPath, $contents);
    }

    protected function flushCaches(): void
    {
        try { Artisan::call('config:clear'); } catch (\Throwable $e) {}
        try { Artisan::call('cache:clear'); }  catch (\Throwable $e) {}
        Cache::flush();
    }
}
