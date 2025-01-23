<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Product;
use App\Models\ProductCart;
use App\Models\ProductWish;
use Illuminate\Http\Request;
use App\Models\ProductDetail;
use App\Models\ProductReview;
use App\Models\ProductSlider;
use App\Helper\ResponseHelper;
use App\Models\CustomerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function ProductCreate(Request $request){

        $validated = Validator::make($request->all(),[
            'title' => 'required|string|min:5|max:200',
            'short_des' => 'required|string|min:5|max:255',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'stock' => 'required|boolean',
            'star' => 'required|numeric',
            'remark' => 'required|in:popular,new,top,special,trending,regular',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'des' => 'required|string|min:5',
            'color' => 'required|string|min:3',
            'size' => 'required',
            'img1' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'img2' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'img3' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'img4' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($validated->fails()){
            return ResponseHelper::Out('fail',$validated->errors(),200);
        }

        DB::beginTransaction();

        try{

            $thumbnail = null;
            $img_one = null;
            $img_two = null;
            $img_three = null;
            $img_four = null;

            if($request->hasFile('image')){
                $thumbnail = 'uploads/products/thumbnail/'.time().'_'.date('d-m-Y').'.'.$request->image->extension();
                $request->image->move(public_path('uploads/products/thumbnail'),$thumbnail);
            }

            if($request->hasFile('img1')){
                $file1 = $request->file('img1');
                $img_one = 'uploads/products/'.time().'_'.date('d-m-Y').'.'.$file1->getClientOriginalExtension();
                $file1->move(public_path('uploads/products'),$img_one);
            }

            if($request->hasFile('img2')){
                $file2 = $request->file('img2');
                $img_two = 'uploads/products/'.time().'_'.date('d-m-Y').'.'.$file2->getClientOriginalExtension();
                $file2->move(public_path('uploads/products'),$img_two);
            }

            if($request->hasFile('img3')){
                $file3 = $request->file('img3');
                $img_three = 'uploads/products/'.time().'_'.date('d-m-Y').'.'.$file3->getClientOriginalExtension();
                $file3->move(public_path('uploads/products'),$img_three);
            }

            if($request->hasFile('img4')){
                $file4 = $request->file('img4');
                $img_four = 'uploads/products/'.time().'_'.date('d-m-Y').'.'.$file4->getClientOriginalExtension();
                $file4->move(public_path('uploads/products'),$img_four);
            }

            $product = Product::create([
                'title' => $request->title,
                'short_des' => $request->short_des,
                'price' => $request->price,
                'discount' => $request->discount,
                'discount_price' => $request->discount_price,
                'image' => $thumbnail,
                'stock' => $request->stock,
                'star' => $request->star,
                'remark' => $request->remark,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id
            ]);

            $productDetail = ProductDetail::create([
                'img1' => $img_one,
                'img2' => $img_two,
                'img3' => $img_three,
                'img4' => $img_four,
                'des' => $request->des,
                'color' => $request->color,
                'size' => $request->size,
                'product_id' => $product->id
            ]);

            DB::commit();

            return ResponseHelper::Out('success','Product Created Successfully',200);

        }
        catch(\Exception $e){

            DB::rollBack();

            return ResponseHelper::Out('fail',$e->getMessage(),200);

        }


    }

    public function ListProductByCategory(Request $request): JsonResponse{

        $data = Product::where('category_id',$request->id)->with('brand','category')->get();

        if($data->isEmpty()){
            return ResponseHelper::Out('fail','No Product Found',200);
        }

        return ResponseHelper::Out('success',$data,200);
    }

    public function ListProductByRemark(Request $request): JsonResponse{
        $data = Product::where('remark',$request->remark)->with('brand','category')->get();

        if($data->isEmpty()){
            return ResponseHelper::Out('fail','No Product Found',200);
        }

        return ResponseHelper::Out('success',$data,200);
    }

    public function ListProductByBrand(Request $request): JsonResponse{
        $data = Product::where('brand_id',$request->id)->with('brand','category')->get();

        if($data->isEmpty()){
            return ResponseHelper::Out('fail','No Product Found',200);
        }

        return ResponseHelper::Out('success',$data,200);
    }

    public function ListProductSlider(Request $request): JsonResponse{
        $data = ProductSlider::all();
        return ResponseHelper::Out('success',$data,200);
    }

    public function ProductDetailsById(Request $request): JsonResponse{

        $data = ProductDetail::where('product_id','=',$request->id)->with('product','brand','category')->get();

        if($data->isEmpty()){
            return ResponseHelper::Out('fail','No Product Found',200);
        }

        return ResponseHelper::Out('success',$data,200);

    }

    public function ListReviewByProduct(Request $request): JsonResponse{

        $data = ProductReview::where('product_id',$request->product_id)->with(['profile'=>function($query){
            $query->select('id','cus_name');
        }])->get();

        if($data->isEmpty()){
            return ResponseHelper::Out('fail','No Review Found',200);
        }

        return ResponseHelper::Out('success',$data,200);

    }

    public function CreateProductReview(Request $request): JsonResponse{
        $user_id = $request->header('id');
        $profile = CustomerProfile::where('user_id', $user_id)->first();

        if($profile){
            $request->merge(['customer_id' => $profile->id]);
            $data = ProductReview::updateOrCreate(['customer_id' => $profile->id,'product_id' => $request->input('product_id')],
            $request->input());
            return ResponseHelper::Out('success',$data,200);
        }
        else{
            return ResponseHelper::Out('fail','Customer profile not exists',200);
        }

    }


    public function ProductWishList(Request $request): JsonResponse{
        $user_id = $request->header('id');
        $data = ProductWish::where('user_id',$user_id)->with('product')->get();
        return ResponseHelper::Out('success',$data,200);
    }

    public function CreateWishList(Request $request): JsonResponse{
        $user_id = $request->header('id');
        $data = ProductWish::updateOrCreate(
            ['user_id' => $user_id,'product_id' => $request->product_id],
            ['user_id' => $user_id,'product_id' => $request->product_id],

        );
        return ResponseHelper::Out('success',$data,200);
    }

    public function RemoveWishList(Request $request): JsonResponse{
        $user_id = $request->header('id');
        $data = ProductWish::where(['user_id' => $user_id,'product_id' => $request->product_id])->delete();
        return ResponseHelper::Out('success',$data,200);
    }

    public function CreateCartList(Request $request): JsonResponse{
        $user_id = $request->header('id');
        $product_id = $request->input('product_id');
        $color = $request->input('color');
        $size = $request->input('size');
        $qty = $request->input('qty');

        $UnitPrice = 0;

        $productDetails = Product::where('id','=',$product_id)->first();

        if($productDetails->discount == 1){
            $UnitPrice = $productDetails->discount_price;
        }
        else{
            $UnitPrice = $productDetails->price;

        }

        $totalPrice = $qty * $UnitPrice;

        $data = ProductCart::updateOrCreate(
            ['user_id' => $user_id,'product_id' => $product_id],
            [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'color' => $color,
                'size' => $size,
                'qty' => $qty,
                'price' => $totalPrice
            ]
        );


        return ResponseHelper::Out('success',$data,200);

    }

    public function CartList(Request $request): JsonResponse{
        $user_id = $request->header('id');
        $data = ProductCart::where('user_id' , $user_id)->with('product')->get();
        return ResponseHelper::Out('success',$data,200);
    }

    public function DeleteCartList(Request $request): JsonResponse{
        $user_id = $request->header('id');
        $data = ProductCart::where('user_id','=',$user_id)->where('product_id','=',$request->product_id)->delete();

        return ResponseHelper::Out('success',$data,200);
    }

}
