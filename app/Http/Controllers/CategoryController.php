<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function CategoryList(): JsonResponse{
        $data = Category::all();
        return ResponseHelper::Out('success',$data,200);
    }
}
