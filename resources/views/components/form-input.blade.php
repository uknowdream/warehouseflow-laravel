@props(['label', 'name', 'type' => 'text', 'value' => null])
<label class="block">
    <span class="text-sm font-medium">{{ $label }}</span>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
           class="mt-1 w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
</label>
