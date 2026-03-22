<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsTemplateExport;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'name'             => 'required|string|max:255|unique:products',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'extra_images'     => 'nullable|array',
            'extra_images.*'   => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available'     => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'category_id'  => $request->category_id,
            'name'         => $request->name,
            'slug'         => Str::slug($request->name),
            'description'  => $request->description,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'image'        => $imagePath,
            'is_available' => $request->boolean('is_available', true),
        ]);

        if ($request->hasFile('extra_images')) {
            foreach ($request->file('extra_images') as $file) {
                $product->productImages()->create([
                    'path' => $file->store('products', 'public'),
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('productImages');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'name'             => 'required|string|max:255|unique:products,name,' . $product->id,
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'extra_images'     => 'nullable|array',
            'extra_images.*'   => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available'     => 'boolean',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'category_id'  => $request->category_id,
            'name'         => $request->name,
            'slug'         => Str::slug($request->name),
            'description'  => $request->description,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'image'        => $imagePath,
            'is_available' => $request->boolean('is_available', true),
        ]);

        // Delete staged image removals
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $img = \App\Models\ProductImage::find($imageId);
                if ($img && $img->product_id === $product->id) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }
        }

        if ($request->hasFile('extra_images')) {
            foreach ($request->file('extra_images') as $file) {
                $product->productImages()->create([
                    'path' => $file->store('products', 'public'),
                ]);
            }
        }

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) Storage::disk('public')->delete($product->image);

        foreach ($product->productImages as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        abort_if($image->product_id !== $product->id, 403);
        Storage::disk('public')->delete($image->path);
        $image->delete();
        return back()->with('success', 'Image removed.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            return redirect()->route('admin.products.index')->with('error', 'No products selected.');
        }

        $products = Product::whereIn('id', $ids)->get();

        foreach ($products as $product) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            foreach ($product->productImages as $img) {
                Storage::disk('public')->delete($img->path);
            }
            $product->delete();
        }

        return redirect()->route('admin.products.index')
            ->with('success', count($products) . ' product(s) deleted.');
    }

    public function bulkUnavailable(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            return redirect()->route('admin.products.index')->with('error', 'No products selected.');
        }

        Product::whereIn('id', $ids)->update(['is_available' => false]);

        return redirect()->route('admin.products.index')
            ->with('success', count($ids) . ' product(s) marked as unavailable.');
    }
    
    public function bulkAvailable(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            return redirect()->route('admin.products.index')->with('error', 'No products selected.');
        }

        Product::whereIn('id', $ids)->update(['is_available' => true]);

        return redirect()->route('admin.products.index')
            ->with('success', count($ids) . ' product(s) marked as available.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $rows     = Excel::toArray([], $request->file('import_file'))[0];
        $heading  = array_map('strtolower', array_map('trim', array_shift($rows)));
        $categories = Category::pluck('id', 'name')->mapWithKeys(
            fn($id, $name) => [strtolower($name) => $id]
        );

        $valid   = [];
        $invalid = [];

        foreach ($rows as $i => $row) {
            $data = array_combine($heading, $row);
            $rowNum = $i + 2;
            $errors = [];

            if (empty(trim($data['name'] ?? '')))           $errors[] = 'Name is required';
            if (!is_numeric($data['price'] ?? ''))          $errors[] = 'Price must be a number';
            if (isset($data['stock']) && !ctype_digit((string)(int)($data['stock']))) $errors[] = 'Stock must be a whole number';

            $catKey = strtolower(trim($data['category'] ?? ''));
            $catId  = $categories[$catKey] ?? null;
            if (!$catId) $errors[] = "Category \"{$data['category']}\" not found";

            $entry = [
                'row'         => $rowNum,
                'name'        => trim($data['name'] ?? ''),
                'description' => trim($data['description'] ?? ''),
                'price'       => $data['price'] ?? '',
                'stock'       => $data['stock'] ?? 0,
                'category'    => trim($data['category'] ?? ''),
                'category_id' => $catId,
            ];

            if ($errors) {
                $entry['errors'] = $errors;
                $invalid[] = $entry;
            } else {
                $valid[] = $entry;
            }
        }

        session(['import_preview' => $valid]);

        return view('admin.products.import-preview', compact('valid', 'invalid'));
    }

    public function confirmImport()
    {
        $rows = session('import_preview', []);

        if (empty($rows)) {
            return redirect()->route('admin.products.index')->with('error', 'Session expired. Please re-upload the file.');
        }

        foreach ($rows as $row) {
            Product::create([
                'category_id'  => $row['category_id'],
                'name'         => $row['name'],
                'slug'         => Str::slug($row['name']) . '-' . Str::random(4),
                'description'  => $row['description'],
                'price'        => (float) $row['price'],
                'stock'        => (int)   $row['stock'],
                'is_available' => 1,
            ]);
        }

        session()->forget('import_preview');

        return redirect()->route('admin.products.index')
            ->with('success', count($rows) . ' product(s) imported successfully.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProductsTemplateExport(), 'products_template.xlsx');
    }

    public function trashed()
    {
        $products = Product::onlyTrashed()->with('category')->latest()->get();
        return view('admin.products.trashed', compact('products'));
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->route('admin.products.trashed')
            ->with('success', "{$product->name} has been restored.");
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        if ($product->image) Storage::disk('public')->delete($product->image);
        foreach ($product->productImages as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->forceDelete();
        return redirect()->route('admin.products.trashed')
            ->with('success', 'Product permanently deleted.');
    }
}