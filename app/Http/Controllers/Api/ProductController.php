<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([]);
        }

        // Search by name or description
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'price']); // only return necessary fields

        return response()->json($products);
    }
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 6);
        $query = Product::with('category');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        $products->getCollection()->transform(function ($product) {
            if ($product->image) {
                if (str_starts_with($product->image, 'http://') || str_starts_with($product->image, 'https://')) {
                    $product->image = $product->image;
                } else {
                    $product->image = asset('storage/' . $product->image);
                }
            }
            return $product;
        });
        
        return response()->json($products);
    }

    public function homeProducts()
    {
        $products = Product::latest()->paginate(8);
        
        $products->getCollection()->transform(function ($product) {
            if ($product->image) {
                if (str_starts_with($product->image, 'http://') || str_starts_with($product->image, 'https://')) {
                    $product->image = $product->image;
                } else {
                    $product->image = asset('storage/' . $product->image);
                }
            }
            return $product;
        });
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        if ($product->image) {
            $product->image = asset('storage/' . $product->image);
        }

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        if ($product->image) {
            if (str_starts_with($product->image, 'http://') || str_starts_with($product->image, 'https://')) {
                $product->image = $product->image;
            } else {
                $product->image = asset('storage/' . $product->image);
            }
        }
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $validated['name'] ?? $product->name,
            'description' => $validated['description'] ?? $product->description,
            'price' => $validated['price'] ?? $product->price,
            'stock' => $validated['stock'] ?? $product->stock,
            'image' => $imagePath,
            'category_id' => array_key_exists('category_id', $validated) ? $validated['category_id'] : $product->category_id,
        ]);

        if ($product->image) {
            if (str_starts_with($product->image, 'http://') || str_starts_with($product->image, 'https://')) {
                $product->image = $product->image;
            } else {
                $product->image = asset('storage/' . $product->image);
            }
        }

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
