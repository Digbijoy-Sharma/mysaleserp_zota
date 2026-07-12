@php
    $isEdit = !empty($product);
    $val = function ($key, $default = '') use ($product) {
        return old($key, $product->{$key} ?? $default);
    };
@endphp

@if($errors->any())
<div class="tw-bg-red-50 tw-border tw-border-red-300 tw-text-red-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4">
    <ul class="tw-list-disc tw-pl-5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Name *</label>
        <input name="name" value="{{ $val('name') }}" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">SKU *</label>
        <input name="sku" value="{{ $val('sku') }}" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Barcode</label>
        <input name="barcode" value="{{ $val('barcode') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Type</label>
        <select name="type" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
            <option value="single" @selected($val('type','single')==='single')>Single</option>
            <option value="variable" @selected($val('type')==='variable')>Variable</option>
        </select>
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Category</label>
        <input name="category" value="{{ $val('category') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Sub Category</label>
        <input name="sub_category" value="{{ $val('sub_category') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Brand</label>
        <input name="brand" value="{{ $val('brand') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Unit</label>
        <input name="unit" value="{{ $val('unit') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full" placeholder="e.g. tablet, ml">
    </div>

    {{-- Pricing --}}
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Default MRP (₹)</label>
        <input name="default_mrp" type="number" step="0.0001" value="{{ $val('default_mrp') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Default Purchase Price (₹)</label>
        <input name="default_purchase_price" type="number" step="0.0001" value="{{ $val('default_purchase_price') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Default Sell Price excl. tax (₹)</label>
        <input name="default_sell_price" type="number" step="0.0001" value="{{ $val('default_sell_price') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Default Sell Price incl. tax (₹)</label>
        <input name="default_sell_price_inc_tax" type="number" step="0.0001" value="{{ $val('default_sell_price_inc_tax') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">GST %</label>
        <input name="default_gst_percent" type="number" step="0.01" value="{{ $val('default_gst_percent') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">HSN code</label>
        <input name="hsn_code" value="{{ $val('hsn_code') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Tax type</label>
        <select name="tax_type" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
            <option value="exclusive" @selected($val('tax_type','exclusive')==='exclusive')>Exclusive</option>
            <option value="inclusive" @selected($val('tax_type')==='inclusive')>Inclusive</option>
        </select>
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Default Alert Quantity</label>
        <input name="default_alert_quantity" type="number" step="0.0001" value="{{ $val('default_alert_quantity') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
</div>

<hr class="tw-my-6">
<h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-3">Pharmacy fields</h3>
<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
    <div class="md:tw-col-span-2">
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Composition</label>
        <textarea name="composition" rows="2" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">{{ $val('composition') }}</textarea>
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Drug schedule *</label>
        <select name="drug_schedule" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
            @foreach(['OTC','H','H1','X','none'] as $s)
            <option value="{{ $s }}" @selected($val('drug_schedule','OTC')===$s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Prescription required</label>
        <select name="prescription_required" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
            <option value="0" @selected($val('prescription_required',0)==0)>No</option>
            <option value="1" @selected($val('prescription_required',0)==1)>Yes</option>
        </select>
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Manufacturer</label>
        <input name="manufacturer" value="{{ $val('manufacturer') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Marketed by</label>
        <input name="marketed_by" value="{{ $val('marketed_by') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Storage condition *</label>
        <select name="storage_condition" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
            @foreach(['ambient','cold','dry','frozen'] as $s)
            <option value="{{ $s }}" @selected($val('storage_condition','ambient')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Package form *</label>
        <select name="package_form" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
            @foreach(['strip','bottle','injection','tube','drops','sachet','other'] as $s)
            <option value="{{ $s }}" @selected($val('package_form','strip')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Units per pack</label>
        <input name="units_per_pack" type="number" min="1" value="{{ $val('units_per_pack',1) }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
</div>

<hr class="tw-my-6">
<h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-3">Flags</h3>
<div class="tw-flex tw-flex-wrap tw-gap-6">
    <label class="tw-flex tw-items-center tw-gap-2">
        <input type="checkbox" name="enable_stock" value="1" @checked($val('enable_stock',1))> Enable stock
    </label>
    <label class="tw-flex tw-items-center tw-gap-2">
        <input type="checkbox" name="is_active" value="1" @checked($val('is_active',1))> Active
    </label>
    <label class="tw-flex tw-items-center tw-gap-2 tw-text-red-700">
        <input type="checkbox" name="is_banned" value="1" @checked($val('is_banned',0))> Banned
    </label>
    <label class="tw-flex tw-items-center tw-gap-2 tw-text-orange-700">
        <input type="checkbox" name="is_discontinued" value="1" @checked($val('is_discontinued',0))> Discontinued
    </label>
</div>

@if(!empty($stores))
<hr class="tw-my-6">
<h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-1">Store assignment</h3>
<p class="tw-text-xs tw-text-gray-500 tw-mb-3">
    Tick which stores should have this product. If none are ticked and <code>dava.central_catalog.auto_assign_all_stores</code> is true, all stores are assigned.
</p>
<div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-2 tw-max-h-64 tw-overflow-y-auto tw-border tw-rounded tw-p-3">
    @foreach($stores as $s)
    <label class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <input type="checkbox" name="store_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, $assigned ?? []))>
        <span>{{ $s->name }} <span class="tw-text-gray-400">({{ $s->store_code ?? '#'.$s->id }})</span></span>
    </label>
    @endforeach
</div>
@endif
