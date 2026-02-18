@if($errors->any())
    <div class="p-3 rounded bg-red-100 text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Code</label>
        <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $coupon->name ?? '') }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="2" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('description', $coupon->description ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="type" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="fixed" @selected(old('type', $coupon->type ?? '')==='fixed')>Fixed</option>
            <option value="percentage" @selected(old('type', $coupon->type ?? '')==='percentage')>Percentage</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Value</label>
        <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value ?? '') }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Min Purchase</label>
        <input type="number" step="0.01" min="0" name="min_purchase" value="{{ old('min_purchase', $coupon->min_purchase ?? '') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Max Discount</label>
        <input type="number" step="0.01" min="0" name="max_discount" value="{{ old('max_discount', $coupon->max_discount ?? '') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Applicable To</label>
        <select name="applicable_to" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="all" @selected(old('applicable_to', $coupon->applicable_to ?? 'all')==='all')>All</option>
            <option value="car" @selected(old('applicable_to', $coupon->applicable_to ?? '')==='car')>Car</option>
            <option value="tour" @selected(old('applicable_to', $coupon->applicable_to ?? '')==='tour')>Tour</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Usage Limit</label>
        <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Usage Per User</label>
        <input type="number" min="1" name="usage_per_user" value="{{ old('usage_per_user', $coupon->usage_per_user ?? 1) }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Valid From</label>
        <input type="date" name="valid_from" value="{{ old('valid_from', isset($coupon) && $coupon->valid_from ? $coupon->valid_from->format('Y-m-d') : '') }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Valid Until</label>
        <input type="date" name="valid_until" value="{{ old('valid_until', isset($coupon) && $coupon->valid_until ? $coupon->valid_until->format('Y-m-d') : '') }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    </div>
    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked((int) old('is_active', $coupon->is_active ?? 1) === 1) class="rounded border-gray-300 text-teal-600">
            <span class="text-sm">Active</span>
        </label>
    </div>
</div>
