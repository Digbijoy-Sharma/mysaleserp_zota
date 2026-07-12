@extends('layouts.superadmin')
@php $current = 'pos-settings'; @endphp

@section('title', 'POS Settings — Dava India')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-4xl tw-mx-auto">

        <div class="tw-mb-6">
            <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900">POS Settings</h1>
            <p class="tw-text-sm tw-text-gray-600">
                Runtime pharmacy flags. Changes take effect on the very next POS request
                (no deploy required). These flags map to <code>.env</code> values:
                <code>DAVA_PHARMACY_FEFO</code>, <code>DAVA_PHARMACY_POS_WARNING</code>,
                <code>DAVA_PHARMACY_BLOCK_EXPIRED</code>, <code>DAVA_CENTRAL_AUTO_ASSIGN</code>,
                <code>DAVA_STOCK_ALERT_DIGEST</code>.
            </p>
        </div>

        @if(session('status'))
            <div class="tw-mb-4 tw-rounded tw-p-3 {{ (session('status.success') ?? 0) ? 'tw-bg-emerald-50 tw-text-emerald-700' : 'tw-bg-rose-50 tw-text-rose-700' }}">
                {{ session('status.msg') }}
            </div>
        @endif

        <form method="POST" action="{{ route('super.settings.pos.save') }}"
              class="tw-bg-white tw-rounded-lg tw-shadow tw-p-6 tw-space-y-5">
            {{ csrf_field() }}

            <div>
                <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer">
                    <input type="hidden" name="DAVA_PHARMACY_FEFO" value="0">
                    <input type="checkbox" name="DAVA_PHARMACY_FEFO" value="1"
                           class="tw-mt-1 tw-size-4"
                           {{ old('DAVA_PHARMACY_FEFO', $flags['DAVA_PHARMACY_FEFO']) ? 'checked' : '' }}>
                    <span>
                        <span class="tw-block tw-text-sm tw-font-semibold tw-text-gray-900">
                            Enforce FEFO at POS
                        </span>
                        <span class="tw-block tw-text-xs tw-text-gray-500">
                            When ON, every sale re-allocates purchase batches in expiry-ascending order
                            (earliest expiring leaves first). Recommended for pharmacy compliance.
                        </span>
                    </span>
                </label>
            </div>

            <hr class="tw-border-gray-200">

            <div>
                <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer">
                    <input type="hidden" name="DAVA_PHARMACY_POS_WARNING" value="0">
                    <input type="checkbox" name="DAVA_PHARMACY_POS_WARNING" value="1"
                           class="tw-mt-1 tw-size-4"
                           {{ old('DAVA_PHARMACY_POS_WARNING', $flags['DAVA_PHARMACY_POS_WARNING']) ? 'checked' : '' }}>
                    <span>
                        <span class="tw-block tw-text-sm tw-font-semibold tw-text-gray-900">
                            Warn on prescription-required / schedule-H drugs
                        </span>
                        <span class="tw-block tw-text-xs tw-text-gray-500">
                            Show a soft warning at the POS when a Schedule H / H1 / X drug is added
                            to the cart without a prescription number on the bill.
                        </span>
                    </span>
                </label>
            </div>

            <hr class="tw-border-gray-200">

            <div>
                <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer">
                    <input type="hidden" name="DAVA_PHARMACY_BLOCK_EXPIRED" value="0">
                    <input type="checkbox" name="DAVA_PHARMACY_BLOCK_EXPIRED" value="1"
                           class="tw-mt-1 tw-size-4"
                           {{ old('DAVA_PHARMACY_BLOCK_EXPIRED', $flags['DAVA_PHARMACY_BLOCK_EXPIRED']) ? 'checked' : '' }}>
                    <span>
                        <span class="tw-block tw-text-sm tw-font-semibold tw-text-gray-900">
                            Hard-block sale of expired batches
                        </span>
                        <span class="tw-block tw-text-xs tw-text-gray-500">
                            When ON, the POS will refuse to save a sale if any line has an expired batch
                            in stock. Staff must mark the batch as damaged/adjusted first.
                        </span>
                    </span>
                </label>
            </div>

            <hr class="tw-border-gray-200">

            <div>
                <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer">
                    <input type="hidden" name="DAVA_CENTRAL_AUTO_ASSIGN" value="0">
                    <input type="checkbox" name="DAVA_CENTRAL_AUTO_ASSIGN" value="1"
                           class="tw-mt-1 tw-size-4"
                           {{ old('DAVA_CENTRAL_AUTO_ASSIGN', $flags['DAVA_CENTRAL_AUTO_ASSIGN']) ? 'checked' : '' }}>
                    <span>
                        <span class="tw-block tw-text-sm tw-font-semibold tw-text-gray-900">
                            Auto-assign new central products to all stores
                        </span>
                        <span class="tw-block tw-text-xs tw-text-gray-500">
                            When ON, every new central product is automatically enabled in every
                            existing store. When OFF, super admin must enable each store manually.
                        </span>
                    </span>
                </label>
            </div>

            <hr class="tw-border-gray-200">

            <div>
                <label class="tw-flex tw-items-start tw-gap-3 tw-cursor-pointer">
                    <input type="hidden" name="DAVA_STOCK_ALERT_DIGEST" value="0">
                    <input type="checkbox" name="DAVA_STOCK_ALERT_DIGEST" value="1"
                           class="tw-mt-1 tw-size-4"
                           {{ old('DAVA_STOCK_ALERT_DIGEST', $flags['DAVA_STOCK_ALERT_DIGEST']) ? 'checked' : '' }}>
                    <span>
                        <span class="tw-block tw-text-sm tw-font-semibold tw-text-gray-900">
                            Daily stock-alert email digest
                        </span>
                        <span class="tw-block tw-text-xs tw-text-gray-500">
                            Send a daily email at <code>DAVA_STOCK_ALERT_DIGEST_TIME</code> (default 09:00 IST)
                            to all super admins summarising low-stock / out-of-stock SKUs across all stores.
                        </span>
                    </span>
                </label>
            </div>

            <div class="tw-flex tw-justify-end tw-pt-4">
                <button type="submit"
                        class="tw-bg-emerald-600 tw-text-white tw-px-5 tw-py-2 tw-rounded-md tw-text-sm tw-font-semibold hover:tw-bg-emerald-700">
                    Save settings
                </button>
            </div>
        </form>

        <div class="tw-mt-6 tw-bg-white tw-rounded-lg tw-shadow tw-p-5 tw-text-xs tw-text-gray-600">
            <h3 class="tw-text-sm tw-font-semibold tw-text-gray-900 tw-mb-2">What these flags actually do</h3>
            <ul class="tw-list-disc tw-pl-5 tw-space-y-1">
                <li><code>DAVA_PHARMACY_FEFO</code> → re-runs batch allocation after every sale using
                    <code>DavaFefoService::planFefoConsumption()</code>.</li>
                <li><code>DAVA_PHARMACY_POS_WARNING</code> → returns
                    <code>dava_warnings</code> in the sale response, surfaced as a toast at POS.</li>
                <li><code>DAVA_PHARMACY_BLOCK_EXPIRED</code> → <code>DavaPosGuard::assertCanSell()</code>
                    throws on any expired batch and the sale is rolled back.</li>
                <li><code>DAVA_CENTRAL_AUTO_ASSIGN</code> → triggers
                    <code>syncStoreAssignments()</code> on every new central product.</li>
                <li><code>DAVA_STOCK_ALERT_DIGEST</code> → enables the
                    <code>dava:daily-stock-alert</code> scheduled command.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
