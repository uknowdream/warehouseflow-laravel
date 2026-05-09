<label class="block"><span class="text-sm font-medium">Produk</span>
<select name="product_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
@foreach($products as $product)
<option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->code }} - {{ $product->name }}</option>
@endforeach
</select></label>

<label class="block"><span class="text-sm font-medium">Warehouse</span>
<select name="warehouse_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
@foreach($warehouses as $warehouse)
<option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
@endforeach
</select></label>

<label class="block"><span class="text-sm font-medium">Lokasi</span>
<select name="location_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
@foreach($locations as $location)
<option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->code }} - {{ $location->name }}</option>
@endforeach
</select></label>
