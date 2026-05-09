<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private const CATEGORY_NAMES = ['Finish Good', 'Raw Material', 'Import'];
    private const UNIT_SYMBOLS = ['kg', 'box'];

    public function index()
    {
        return view('products.index', [
            'products' => Product::with(['category', 'unit'])->latest()->paginate(15)
        ]);
    }

    public function create()
    {
        return view('products.form', [
            'product' => new Product(),
            'categories' => $this->categoryOptions(),
            'units' => $this->unitOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['qr_code'] = 'PRODUCT:' . $data['code'];

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        return view('products.form', [
            'product' => $product,
            'categories' => $this->categoryOptions(),
            'units' => $this->unitOptions(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product->id);
        $data['qr_code'] = 'PRODUCT:' . $data['code'];

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'code' => ['required', 'string', 'max:100', 'unique:products,code,' . $ignoreId],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    public function qr(Product $product)
    {
        return view('products.qr', compact('product'));
    }

    private function categoryOptions()
    {
        return Category::whereIn('name', self::CATEGORY_NAMES)
            ->get()
            ->sortBy(fn (Category $category) => array_search($category->name, self::CATEGORY_NAMES, true))
            ->values();
    }

    private function unitOptions()
    {
        return Unit::whereIn('symbol', self::UNIT_SYMBOLS)
            ->get()
            ->sortBy(fn (Unit $unit) => array_search($unit->symbol, self::UNIT_SYMBOLS, true))
            ->values();
    }
}
