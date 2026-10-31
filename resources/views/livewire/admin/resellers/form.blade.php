<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $reseller?->exists ? 'Edit Reseller' : 'New Reseller' }}</h2>
        <a href="{{ route('admin.resellers.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Name <span class="text-red-400">*</span></label><input type="text" wire:model="form.name" class="w-full rounded-lg border-gray-300 text-sm @error('form.name') border-red-500 @enderror">@error('form.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Region <span class="text-red-400">*</span></label><select wire:model="form.region_id" class="w-full rounded-lg border-gray-300 text-sm @error('form.region_id') border-red-500 @enderror"><option value="">— Select —</option>@foreach($regions as $r)<option value="{{ $r->id }}">{{ $r->getTranslation('name', app()->getLocale()) }}</option>@endforeach</select>@error('form.region_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Type</label><select wire:model="form.type" class="w-full rounded-lg border-gray-300 text-sm"><option value="reseller">Reseller</option><option value="service_center">Service Center</option><option value="both">Both</option></select></div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Phone</label><input type="text" wire:model="form.phone" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Phone 2</label><input type="text" wire:model="form.phone2" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Email</label><input type="email" wire:model="form.email" class="w-full rounded-lg border-gray-300 text-sm @error('form.email') border-red-500 @enderror">@error('form.email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Website</label><input type="url" wire:model="form.website" class="w-full rounded-lg border-gray-300 text-sm @error('form.website') border-red-500 @enderror">@error('form.website') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Latitude</label><input type="text" wire:model="form.latitude" placeholder="e.g. 41.311081" class="w-full rounded-lg border-gray-300 text-sm @error('form.latitude') border-red-500 @enderror">@error('form.latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Longitude</label><input type="text" wire:model="form.longitude" placeholder="e.g. 69.240562" class="w-full rounded-lg border-gray-300 text-sm @error('form.longitude') border-red-500 @enderror">@error('form.longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Sort Order</label><input type="number" wire:model="form.sort_order" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_active"> Active</label></div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-sm font-semibold mb-3">Address</p>
            <div class="flex gap-2 mb-4 border-b border-gray-200">@foreach(['uz'=>"O'zbek",'ru'=>'Русский','en'=>'English'] as $code => $label)<button type="button" @click="locale='{{ $code }}'" :class="locale==='{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400'" class="pb-2 px-3 text-sm transition">{{ $label }}</button>@endforeach</div>
            @foreach(['uz','ru','en'] as $code)<div x-show="locale==='{{ $code }}'"><textarea wire:model="form.address.{{ $code }}" rows="2" placeholder="Address ({{ $code }})" class="w-full rounded-lg border-gray-300 text-sm"></textarea></div>@endforeach
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">Save</button>
            <a href="{{ route('admin.resellers.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
</div>
