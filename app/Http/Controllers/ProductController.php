<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function list(Request $request)
    {
        $query = Product::query();

        if ($request->filled('unique_key')) {
            $query->where('unique_key', $request->unique_key);
        }

        $products = $query->orderBy('id')->get();

        return response()->json(['data' => $products]);
    }
}
