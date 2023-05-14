<?php

namespace App\Http\Controllers\api;

use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductGallery;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['galleries', 'sizes'])->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'cate_id' => 'required',
            'price' => 'required',
            'type' => 'required',
            'description' => 'required',
            'sale' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $product = new Product;
        $product->name = $request->name;
        $product->image = $request->image;
        $product->cate_id = $request->cate_id;
        $product->price = $request->price;
        $product->type = $request->type;
        $product->description = $request->description;
        $product->sale = $request->sale;
        $product->save();
        if ($request->sizes) {
            foreach ($request->sizes as $key => $val) {
                $product_size = new ProductSize();
                $product_size->product_id = $product->id;
                $product_size->size = $val['size'];
                if ($val['quantity_sold']) {
                    $product_size->quantity_sold = $val['quantity_sold'];
                } else {
                    $product_size->quantity_sold = 0;
                }
                $product_size->quantity_remaining = $val['quantity_remaining'];
                $product_size->save();
            }
        };
        if ($request->galleries) {
            foreach ($request->galleries as $key => $val) {
                $product_gallery = new ProductGallery();
                $product_gallery->product_id = $product->id;
                $product_gallery->thumb = $val['thumb'];
                $product_gallery->save();
            }
        }
        return response()->json($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with(['galleries', 'sizes'])
            ->where('id', $id)
            ->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name,' . $id,
            'image' => 'required',
            'cate_id' => 'required',
            'price' => 'required',
            'type' => 'required',
            'description' => 'required',
            'sale' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        $product->name = $request->name;
        $product->image = $request->image;
        $product->cate_id = $request->cate_id;
        $product->price = $request->price;
        $product->type = $request->type;
        $product->description = $request->description;
        $product->sale = $request->sale;
        $product->save();
        if ($request->sizes) {
            foreach ($request->sizes as $key => $val) {
                // Delete all existing sizes for the product
                $product->sizes()->delete();
                foreach ($request->sizes as $key => $val) {
                    $product_size = new ProductSize();
                    $product_size->product_id = $product->id;
                    $product_size->size = $val['size'];
                    $product_size->quantity_sold = $val['quantity_sold'];
                    $product_size->quantity_remaining = $val['quantity_remaining'];
                    $product_size->save();
                }
            }
        };
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getProductByCate($categoryId = null)
    {
        if ($categoryId) {
            $products = Product::where('cate_id', $categoryId)->get();
        } else {
            $products = Product::all();
        }
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
