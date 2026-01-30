<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $query = Category::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status === 'active') {
            $query->where('active', true);
        } elseif ($status === 'archived') {
            $query->where('active', false);
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('categories.index', compact('categories', 'search', 'status'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $this->categoryRepository->create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $this->categoryRepository->update($category->id, $validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        $this->categoryRepository->delete($category->id);

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(Category $category)
    {
        $this->categoryRepository->toggleActive($category->id);

        $status = $category->fresh()->active ? 'activated' : 'deactivated';

        return redirect()->route('categories.index')
            ->with('success', "Category {$status} successfully.");
    }
}
