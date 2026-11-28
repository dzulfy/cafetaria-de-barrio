<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with('menus')->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }
}
