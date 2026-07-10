@php
    $isEdit = !empty($supplier);
    $val = function ($key, $default = '') use ($supplier) {
        return old($key, $supplier->{$key} ?? $default);
    };
@endphp

@if($errors->any())
<div class="tw-bg-red-50 tw-border tw-border-red-300 tw-text-red-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4">
    <ul class="tw-list-disc tw-pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Name *</label>
        <input name="name" value="{{ $val('name') }}" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Code</label>
        <input name="code" value="{{ $val('code') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Contact person</label>
        <input name="contact_person" value="{{ $val('contact_person') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Email</label>
        <input name="email" type="email" value="{{ $val('email') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Mobile</label>
        <input name="mobile" value="{{ $val('mobile') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">GSTIN</label>
        <input name="gstin" value="{{ $val('gstin') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Drug license no</label>
        <input name="drug_license_no" value="{{ $val('drug_license_no') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">FSSAI no</label>
        <input name="fssai_no" value="{{ $val('fssai_no') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div class="md:tw-col-span-2">
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Address line 1</label>
        <input name="address_line1" value="{{ $val('address_line1') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div class="md:tw-col-span-2">
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Address line 2</label>
        <input name="address_line2" value="{{ $val('address_line2') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">City</label>
        <input name="city" value="{{ $val('city') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">State</label>
        <input name="state" value="{{ $val('state') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Pincode</label>
        <input name="pincode" value="{{ $val('pincode') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Country</label>
        <input name="country" value="{{ $val('country', 'India') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Lead time (days)</label>
        <input name="lead_time_days" type="number" min="0" value="{{ $val('lead_time_days', 7) }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Credit limit (₹)</label>
        <input name="credit_limit" type="number" step="0.01" value="{{ $val('credit_limit') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div class="md:tw-col-span-2">
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Payment terms</label>
        <input name="payment_terms" value="{{ $val('payment_terms') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Bank name</label>
        <input name="bank_name" value="{{ $val('bank_name') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Bank account no</label>
        <input name="bank_account_no" value="{{ $val('bank_account_no') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div>
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Bank IFSC</label>
        <input name="bank_ifsc" value="{{ $val('bank_ifsc') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
    </div>
    <div class="md:tw-col-span-2">
        <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Notes</label>
        <textarea name="notes" rows="2" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">{{ $val('notes') }}</textarea>
    </div>
    <div>
        <label class="tw-flex tw-items-center tw-gap-2">
            <input type="checkbox" name="is_active" value="1" @checked($val('is_active', 1))> Active
        </label>
    </div>
</div>

@if(!empty($stores))
<hr class="tw-my-6">
<h3 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-1">Store assignment</h3>
<p class="tw-text-xs tw-text-gray-500 tw-mb-3">Tick which stores can purchase from this vendor.</p>
<div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-2 tw-max-h-64 tw-overflow-y-auto tw-border tw-rounded tw-p-3">
    @foreach($stores as $s)
    <label class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <input type="checkbox" name="store_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, $assigned ?? []))>
        <span>{{ $s->name }} <span class="tw-text-gray-400">({{ $s->store_code ?? '#'.$s->id }})</span></span>
    </label>
    @endforeach
</div>
@endif
