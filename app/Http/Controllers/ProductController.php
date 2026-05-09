<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const CATEGORY_NAMES = ['Finish Good', 'Raw Material', 'Import'];
    private const UNIT_SYMBOLS = ['kg', 'box'];

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $unitId = $request->query('unit_id');
        $status = $request->query('status');

        return view('products.index', [
            'products' => Product::query()
                ->with(['category', 'unit'])
                ->withSum('stockBalances as total_stock', 'qty')
                ->when($search, function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('qr_code', 'like', "%{$search}%");
                    });
                })
                ->when($categoryId, fn ($query, string $categoryId) => $query->where('category_id', $categoryId))
                ->when($unitId, fn ($query, string $unitId) => $query->where('unit_id', $unitId))
                ->when($status === 'active', fn ($query) => $query->where('is_active', true))
                ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'categories' => $this->categoryOptions(),
            'units' => $this->unitOptions(),
            'search' => $search,
            'selectedCategory' => $categoryId,
            'selectedUnit' => $unitId,
            'selectedStatus' => $status,
            'summary' => [
                'total' => Product::count(),
                'active' => Product::where('is_active', true)->count(),
                'inactive' => Product::where('is_active', false)->count(),
            ],
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

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->stockBalances()->where('qty', '>', 0)->exists() || $product->stockMoves()->exists() || $product->stockOpnameLines()->exists()) {
            return back()->withErrors('Produk sudah dipakai transaksi. Nonaktifkan produk jika tidak ingin digunakan lagi.');
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'code' => ['required', 'string', 'max:100', 'unique:products,code,' . $ignoreId],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
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
