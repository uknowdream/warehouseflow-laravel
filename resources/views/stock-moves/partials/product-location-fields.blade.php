<label><span class="text-sm font-medium">Produk</span>
<select name="product_id" class="mt-1 w-full rounded-xl border-slate-300">
@foreach($products as $product)
<option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
@endforeach
</select></label>

<label><span class="text-sm font-medium">Warehouse</span>
<select name="warehouse_id" class="mt-1 w-full rounded-xl border-slate-300">
@foreach($warehouses as $warehouse)
<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
@endforeach
</select></label>

<label><span class="text-sm font-medium">Lokasi</span>
<select name="location_id" class="mt-1 w-full rounded-xl border-slate-300">
@foreach($locations as $location)
<option value="{{ $location->id }}">{{ $location->code }} - {{ $location->name }}</option>
@endforeach
</select></label>
