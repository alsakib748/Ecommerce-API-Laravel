<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Validator;

class BrandController extends Controller
{

    public function BrandCreate(Request $request){

        $validated = Validator::make($request->all(),[
            'brandName' => 'required|string|max:100',
            'brandImg' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validated->fails()){
            return ResponseHelper::Out('fail',$validated->errors(),200);
        }

        $brand = new Brand();
        $brand->brandName = $request->brandName;

        if($request->hasFile('brandImg')){
            $file = $request->file('brandImg');
            $extension = $file->getClientOriginalExtension();
            $filename = 'uploads/brands/'.time().'_'.date('Y-m-d').'.'.$extension;
            $file->move(public_path('uploads/brands'),$filename);
            $brand->brandImg = $filename;
        }

        $brand->save();

        return ResponseHelper::Out('success',$brand,200);

    }

    public function BrandList(): JsonResponse{
        $data = Brand::all();
        return ResponseHelper::Out('success',$data, 200);
    }

    public function BrandEdit($id){

        $data = Brand::find($id);

        if(!$data){
            return ResponseHelper::Out('fail','Brand not found',200);
        }

        return ResponseHelper::Out('success',[ 'data' => $data ],200);

    }

    public function BrandUpdate(Request $request){

        $validated = Validator::make($request->all(),[
            'id' => 'required|integer',
            'brandName' => 'required|string|max:100',
            'brandImg' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validated->fails()){
            return ResponseHelper::Out('fail',$validated->errors(),200);
        }

        $brand = Brand::find($request->id);

        if(!$brand){
            return ResponseHelper::Out('fail','Brand not found',200);
        }

        $brand->brandName = $request->brandName;

        if($request->hasFile('brandImg')){

            if(file_exists($brand->brandImg)){
                unlink($brand->brandImg);
            }

            $file = $request->file('brandImg');
            $extension = $file->getClientOriginalExtension();
            $filename = 'uploads/brands/'.time().'_'.date('Y-m-d').'.'.$extension;
            $file->move(public_path('uploads/brands'),$filename);
            $brand->brandImg = $filename;
        }

        $brand->save();

        return ResponseHelper::Out('success','Brand Update Successfully',200);

    }

    public function BrandDelete(Request $request){

        $id = $request->input('id');

        $brand = Brand::find($id);

        if(!$brand){
            return ResponseHelper::Out('fail','Brand not found',200);
        }

        if(file_exists($brand->brandImg)){
            unlink($brand->brandImg);
        }

        $brand->delete();

        return ResponseHelper::Out('success','Brand Delete Successfully',200);

    }

}

