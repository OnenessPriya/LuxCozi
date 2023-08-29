<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product=Product::where('collection_id',$id)->where('status',1)->orderby('position')->get();
        if ($product) {
            return response()->json(['error'=>false, 'resp'=>'Collection List fetched successfully','data'=>$product]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
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
        //
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

    /**
     * Display the specified colors.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function colors($id)
    {
        $color=ProductColorSize::where('product_id',$id)->where('status',1)->with('color:id,name')->get();
        if ($color) {
            return response()->json(['error'=>false, 'resp'=>'Color List fetched successfully','data'=>$color]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    /**
     * Display the specified sizes.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sizes($id)
    {
        $size=ProductColorSize::where('product_id',$id)->where('status',1)->with('size:id,name')->get();
        if ($size) {
            return response()->json(['error'=>false, 'resp'=>'Size List fetched successfully','data'=>$size]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
}
