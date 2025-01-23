<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Validator;

class CategoryController extends Controller
{

    public function CategoryCreate(Request $request){

        $validated = Validator::make($request->all(),[
            'categoryName' => 'required|string|max:100',
            'categoryImg' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validated->fails()){
            return ResponseHelper::Out('fail',$validated->errors(),200);
        }

        $category = new Category();
        $category->categoryName = $request->categoryName;

        if($request->hasFile('categoryImg')){
            $file = $request->file('categoryImg');
            $extension = $file->getClientOriginalExtension();
            $filename = 'uploads/categories/'.time().'_'.date('Y-m-d').'.'.$extension;
            $file->move(public_path('uploads/categories'),$filename);
            $category->categoryImg = $filename;
        }

        $category->save();

        return ResponseHelper::Out('success',$category,200);

    }

    public function CategoryList(): JsonResponse{
        $data = Category::all();
        return ResponseHelper::Out('success',$data,200);
    }

    public function CategoryEdit($id){

        $data = Category::find($id);

        if(!$data){
            return ResponseHelper::Out('fail','Category not found',200);
        }

        return ResponseHelper::Out('success',[ 'data' => $data ],200);

    }

    public function CategoryUpdate(Request $request){

        $validated = Validator::make($request->all(),[
            'id' => 'required|integer',
            'categoryName' => 'required|string|max:100',
            'categoryImg' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validated->fails()){
            return ResponseHelper::Out('fail',$validated->errors(),200);
        }

        $category = Category::find($request->id);

        if(!$category){
            return ResponseHelper::Out('fail','Category not found',200);
        }

        $category->categoryName = $request->categoryName;

        if($request->hasFile('categoryImg')){

            if(file_exists($category->categoryImg)){
                unlink($category->categoryImg);
            }

            $file = $request->file('categoryImg');
            $extension = $file->getClientOriginalExtension();
            $filename = 'uploads/categories/'.time().'_'.date('Y-m-d').'.'.$extension;
            $file->move(public_path('uploads/categories'),$filename);
            $category->categoryImg = $filename;
        }

        $category->save();

        return ResponseHelper::Out('success','Category Update Successfully',200);

    }

    public function CategoryDelete(Request $request){

        $id = $request->input('id');

        $category = Category::find($id);

        if(!$category){
            return ResponseHelper::Out('fail','Category not found',200);
        }

        if(file_exists($category->categoryImg)){
            unlink($category->categoryImg);
        }

        $category->delete();

        return ResponseHelper::Out('success','Category Delete Successfully',200);

    }


}
