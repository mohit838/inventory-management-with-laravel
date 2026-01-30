<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SubcategoryRepository;
use App\Services\MinioService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductRepository $productRepository;
    protected CategoryRepository $categoryRepository;
    protected SubcategoryRepository $subcategoryRepository;
    protected MinioService $minioService;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        SubcategoryRepository $subcategoryRepository,
        MinioService $minioService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->subcategoryRepository = $subcategoryRepository;
        $this->minioService = $minioService;
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $categoryId = $request->get('category_id', '');
        $status = $request->get('status', '');

        $query = Product::with('category');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($status === 'active') {
            $query->where('active', true);
        } elseif ($status === 'inactive') {
            $query->where('active', false);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::where('active', true)->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'status'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        $subcategories = \App\Models\Subcategory::where('active', true)->orderBy('name')->get();

        return view('products.create', compact('categories', 'subcategories'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $this->minioService->uploadFile($file, 'products', 'public');
            $validated['image_url'] = $path;
        }

        $this->productRepository->create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load(['category', 'subcategory']);
        
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        $subcategories = \App\Models\Subcategory::where('active', true)->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'subcategories'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $this->minioService->uploadFile($file, 'products', 'public');
            $validated['image_url'] = $path;
        }

        $this->productRepository->update($product->id, $validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        $this->productRepository->delete($product->id);

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product active status
     */
    public function toggleActive(Product $product)
    {
        $this->productRepository->toggleActive($product->id);

        $status = $product->fresh()->active ? 'activated' : 'deactivated';

        return redirect()->route('products.index')
            ->with('success', "Product {$status} successfully.");
    }
}
