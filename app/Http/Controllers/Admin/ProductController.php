<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductColorSize;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Support\Facades\Validator;
use DB;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = (!empty($request->keyword) && $request->keyword!='')?$request->keyword:'';
        $collection = (!empty($request->collection_id) && $request->collection_id!='')?$request->collection_id:'';
        $category = (!empty($request->cat_id) && $request->cat_id!='')?$request->cat_id:'';
        $data=Product::when($keyword!='', function($query) use ($keyword){
            $query->where('name', 'LIKE', '%' . $keyword . '%')->orWhere('style_no', 'LIKE', '%' . $keyword . '%');
            })
            ->when($collection!='', function($query) use ($collection){
                $query->where('collection_id', '=', $collection);
        })
        ->when($category!='', function($query) use ($category){
            $query->where('cat_id', '=' , $category);
        })
        ->latest('id','desc')->paginate(25);
        $category =Category::where('status', 1)->orderBy('position')->get();
        return view('admin.product.index',compact('data','category','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category =Category::where('status', 1)->orderBy('position')->get();
        $colors = Color::all();
        $sizes = Size::all();
        return view('admin.product.create',compact('category','colors','sizes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            "cat_id" => "required|integer",
            "collection_id" => "required|integer",
            "name" => "required|string|max:255",
            "short_desc" => "nullable",
            "desc" => "nullable",
            "price" => "nullable|integer",
            "offer_price" => "nullable|integer",
            "meta_title" => "nullable",
            "meta_desc" => "nullable",
            "meta_keyword" => "nullable",
            "style_no" => "nullable",
            "image" => "required",
            "color_id" => "nullable|array",
            "size_id" => "nullable|array",
        ]);

             $collectedData = $request->except('_token');
			$count = Product::latest('id')->count();
            $newEntry = new Product;
            $newEntry->cat_id = $collectedData['cat_id'];
            $newEntry->collection_id = $collectedData['collection_id'];
            $newEntry->name = $collectedData['name'];
            $newEntry->short_desc = $collectedData['short_desc'];
            $newEntry->desc = $collectedData['desc'];
            $newEntry->price = $collectedData['price'];
            $newEntry->offer_price = $collectedData['offer_price'];
            $newEntry->meta_title = $collectedData['meta_title'];
            $newEntry->meta_desc = $collectedData['meta_desc'];
            $newEntry->meta_keyword = $collectedData['meta_keyword'];
            $newEntry->style_no = $collectedData['style_no'];
            $newEntry->position = $count+1;
            // slug generate
            $newEntry->slug = slugGenerate($collectedData['name'],'products');

            // main image handling
            $upload_path = "public/uploads/product/";
            if(isset($collectedData['image'])){
                $image = $collectedData['image'];
                $imageName = time() . "." . $image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $newEntry->image = $upload_path . $uploadedImage;
                
            }
            $newEntry->save();
            if (!empty($collectedData['color_id']) && !empty($collectedData['size_id'])) {
                $multipleColorData = [];

                foreach ($collectedData['color_id'] as $colorKey => $colorValue) {
                    $multipleColorData[] = [
                        'product_id' => $newEntry->id,
                        'color_id' => $colorValue,
                        'price'=>$newEntry->price,
                        'offer_price'=>$newEntry->offer_price,
                        'created_at' =>date('Y-m-d H:i:s'),
                        'updated_at' =>date('Y-m-d H:i:s'),
                    ];
                }

                foreach ($collectedData['size_id'] as $sizeKey => $sizeValue) {
                    $multipleColorData[$sizeKey]['size_id'] = $sizeValue;
                }

                // dd($multipleColorData);

                ProductColorSize::insert($multipleColorData);
            }
        if ($newEntry) {
            return redirect()->route('admin.products.index');
        } else {
            return redirect()->route('admin.products.create')->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=Product::where('id',$id)->with('colorSize','category','collection')->first();
        $images = ProductImage::where('product_id', $id)->latest('id')->get();
        return view('admin.product.detail', compact('data', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=Product::where('id',$id)->with('colorSize','category','collection')->first();
        $category =Category::where('status', 1)->orderBy('position')->get();
        $images = ProductImage::where('product_id', $id)->latest('id')->get();
        $colors = Color::all();
        $sizes = Size::all();
        $productColorGroup = ProductColorSize::select('id', 'color_id', 'status')->where('product_id', $id)->groupBy('color_id')->orderBy('id')->get();
        return view('admin.product.edit', compact('data', 'category','colors','sizes','images','productColorGroup','id'));
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
        $request->validate([
            "cat_id" => "required|integer",
            "collection_id" => "nullable|integer",
            "name" => "required|string|max:255",
            "short_desc" => "nullable",
            "desc" => "nullable",
            "price" => "nullable|integer",
            "offer_price" => "nullable|integer",
            "meta_title" => "nullable",
            "meta_desc" => "nullable",
            "meta_keyword" => "nullable",
            "style_no" => "nullable",
            "image" => "nullable",
            "color_id" => "nullable|array",
            "size_id" => "nullable|array",
        ]);

            $collectedData = $request->except('_token');
			$count = Product::latest('id')->count();
            $newEntry =  Product::findOrfail($id);
            $newEntry->cat_id = $collectedData['cat_id'] ?? '';
            if(!empty($collectedData['collection_id'])){
             $newEntry->collection_id = $collectedData['collection_id'] ?? '';
            }
            $newEntry->name = $collectedData['name'] ?? '';
            $newEntry->short_desc = $collectedData['short_desc'] ?? '';
            $newEntry->desc = $collectedData['desc']?? '';
            $newEntry->price = $collectedData['price']?? '';
            $newEntry->offer_price = $collectedData['offer_price']?? '';
            $newEntry->meta_title = $collectedData['meta_title']?? '';
            $newEntry->meta_desc = $collectedData['meta_desc']?? '';
            $newEntry->meta_keyword = $collectedData['meta_keyword']?? '';
            $newEntry->style_no = $collectedData['style_no']?? '';
            // slug generate
            if($newEntry->name != $collectedData['name']){
             $newEntry->slug = slugGenerate($collectedData['name'],'products');
            }

            // main image handling
            $upload_path = "public/uploads/product/";
            
            if(isset($collectedData['image'])){
                $image = $collectedData['image'];
                $imageName = time() . "." . $image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $newEntry->image = $upload_path . $uploadedImage;
                
            }
            $newEntry->save();
            
        if ($newEntry) {
            return redirect()->route('admin.products.index')->with('success', 'Product Updated Successfully');
        } else {
            return redirect()->route('admin.products.create')->withInput($request->all());
        }
    }
    /**
     * status change the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        $data = Product::findOrFail($id);
        $status = ( $data->status == 1 ) ? 0 : 1;
        $data->status = $status;
        $data->save();
        if ($data) {
            return redirect()->route('admin.products.index');
        } else {
            return redirect()->route('admin.products.create')->withInput($request->all());
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data=Product::destroy($id);
        if ($data) {
            return redirect()->route('admin.products.index');
        } else {
            return redirect()->route('admin.products.index')->withInput($request->all());
        }
    }
     /**
     * csv export the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function csvExport(Request $request)
    {
         $data = DB::select("SELECT p.name, p.style_no, cls.name AS collection_name, ctr.name AS category_name, c.name AS org_color, pcs.color_id, s.name AS org_size, pcs.size_id, pcs.price FROM product_color_sizes AS pcs INNER JOIN colors AS c ON c.id = pcs.color_id INNER JOIN sizes AS s ON s.id = pcs.size_id INNER JOIN products AS p ON p.id = pcs.product_id INNER JOIN collections AS cls ON cls.id = p.collection_id INNER JOIN categories AS ctr ON ctr.id = p.cat_id");

        // dd($data[0]->orderDetails->id);

        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "Lux-all-products-" . date('Y-m-d') . ".csv";

            // Create a file pointer 
            $f = fopen('php://memory', 'w');

            // Set column headers 
            $fields = array('SR', 'NAME', 'STYLE NUMBER', 'COLLECTION', 'CATEGORY', 'COLOR', 'SIZE', 'PRICE');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach ($data as $row) {
                $color = $row->org_color;
                $size =  $row->org_size;

                $lineData = array(
                    $count,
                    $row->name ?? '',
                    $row->style_no ?? '',
                    $row->collection_name ?? '',
                    $row->category_name ?? '',
                    $color,
                    $size,
                    'Rs. ' . number_format($row->price) ?? '0',
                   
                );

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function size(Request $request)
    {
        $productId = $request->productId;
        $colorId = $request->colorId;

        $data = ProductColorSize::where('product_id', $productId)->where('color_id', $colorId)->get();

        $resp = [];

        foreach ($data as $dataKey => $dataValue) {
            $resp[] = [
                'variationId' => $dataValue->id,
                'sizeId' => $dataValue->size_id,
                'sizeName' => $dataValue->size->name
            ];
        }

        return response()->json(['error' => false, 'data' => $resp]);
    }
    //variation edit
    public function variationBulkEdit(Request $request)
    {
        $request->validate([
            "bulkAction" => "required | in:edit",
            "variation_id" => "required | array",
        ]);
        $data = $request->variation_id;

        return view('admin.product.bulk.edit', compact('data', 'request'));
    }
    //variation update
    public function variationBulkUpdate(Request $request)
    {
        // dd($request->all());

        $request->validate([
            "id" => "required|array",
            "offer_price" => "required|array"
        ]);

        // dd('here');

        foreach ($request->id as $key => $value) {
            $offer_price = $request->offer_price[$key];

            DB::table('product_color_sizes')
                ->where('id', $value)
                ->update([
                    'offer_price' => $offer_price
                ]);
        }

        return redirect()->route('admin.products.edit', $request->product_id)->with('success', 'Bulk update successfull');
    }

    public function destroySingleImage(Request $request, $id)
    {
        $image=ProductImage::destroy($id);
        return redirect()->back();

       
    }
    public function bulkDestroy(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'bulk_action' => 'required',
            'delete_check' => 'required|array',
        ], [
            'delete_check.*' => 'Please select at least one item'
        ]);

        if (!$validator->fails()) {
            if ($request['bulk_action'] == 'delete') {
                foreach ($request->delete_check as $index => $delete_id) {
                    Product::where('id', $delete_id)->delete();
                }

                return redirect()->route('admin.products.index')->with('success', 'Selected items deleted');
            } else {
                return redirect()->route('admin.products.index')->with('failure', 'Please select an action')->withInput($request->all());
            }
        } else {
            return redirect()->route('admin.products.index')->with('failure', $validator->errors()->first())->withInput($request->all());
        }
    }

    public function variationSizeDestroy(Request $request, $id)
    {
        // dd($id);
        ProductColorSize::destroy($id);
        return redirect()->back()->with('success', 'Size deleted successfully');
    }

    public function variationImageDestroy(Request $request)
    {
        // dd($request->all());
        ProductImage::destroy($request->id);
        return response()->json(['status' => 200, 'message' => 'Image deleted successfully']);
      
    }

    public function variationImageUpload(Request $request)
    {
         //dd($request->all());

        $request->validate([
            'product_id' => 'required',
            'color_id' => 'required',
            'image' => 'required|array',
        ]);

        $product_id = $request->product_id;
        $color_id = $request->color_id;

       
        foreach ($request->image as $imageKey => $imageValue) {
            $newName = mt_rand() . '_' . time() . '.' . $imageValue->getClientOriginalExtension();
            $imageValue->move('uploads/product/product_images/', $newName);

            $productImage = new ProductImage();
            $productImage->product_id = $product_id;
            $productImage->color_id = $color_id;
            $productImage->image = 'uploads/product/product_images/' . $newName;
            $productImage->save();
        }

        return redirect()->back()->with('success', 'Images added successfully!');
    }

    public function variationSizeUpload(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'color_id' => 'required',
            'size_id' => 'required',
            'price' => 'required',
            'offer_price' => 'required',
        ]);

        if (!$validator->fails()) {
            $productImage = new ProductColorSize();
            $productImage->product_id = $request->product_id;
            $productImage->color_id = $request->color_id;
            $productImage->size_id = $request->size_id;
            $productImage->assorted_flag = $request->assorted_flag ? $request->assorted_flag : 0;
            $productImage->price = $request->price;
            $productImage->offer_price = $request->offer_price;
            $productImage->stock = $request->stock ? $request->stock : 0;
            $productImage->code = $request->code ? $request->code : 0;
            $productImage->save();
            return redirect()->back();
        } else {
            return redirect()->back()->with('failure', $validator->errors()->first())->withInput($request->all());
        }

       
    }

    public function variationColorDestroy(Request $request, $productId, $colorId)
    {
        // dd($productId, $colorId);
        ProductColorSize::where('product_id', $productId)->where('color_id', $colorId)->delete();
        return redirect()->back()->with('success', 'Color variation deleted!');
    }

    public function variationColorAdd(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'color_id' => 'required',
            'size_id' => 'required',
            'price' => 'nullable',
            'offer_price' => 'nullable',
            'sku_code' => 'nullable|unique:product_color_sizes,code',
        ]);

        if (!$validator->fails()) {

            $check = ProductColorSize::where('product_id', $request->product_id)->where('color_id', $request->color_id)->where('size_id', $request->size_id)->count();

            if ($check == 0) {
                $colorName = Color::select('name')->where('id', $request->color_id)->first();
                $sizeName = Size::select('name')->where('id', $request->size_id)->first();

                $productImage = new ProductColorSize();
                $productImage->product_id = $request->product_id;
                $productImage->color_id = $request->color_id;
                $productImage->size_id = $request->size_id;
                $productImage->assorted_flag = $request->assorted_flag ? $request->assorted_flag : 0;
                $productImage->price = $request->price ?? 0;
                $productImage->offer_price = $request->offer_price ?? $request->price;
                $productImage->save();

                return redirect()->back()->with('success', 'Color added successfully');
            } else {
                return redirect()->back()->with('failure', 'This color & size already exist. Select a different one.')->withInput($request->all());
            }
        } else {
            return redirect()->back()->with('failure', $validator->errors()->first())->withInput($request->all());
        }

       
    }

    public function variationColorRename(Request $request)
    {
         
        $request->validate([
            'product_id' => 'required|integer',
            'current_color2' => 'required|integer',
            'update_color_name' => 'required'
        ]);

        
        Color::where('id', $request->current_color2)->update(['name' => $request->update_color_name]);

        return redirect()->back()->with('success', 'Color name updated');
    }

    public function variationColorEdit(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'product_id' => 'required|integer',
            'current_color' => 'required|integer',
            'update_color' => 'required|integer'
        ]);

        $colorsCHeck = ProductColorSize::select('color_id')->where('product_id', $request->product_id)->groupBy('color_id')->pluck('color_id')->toArray();

        if (in_array($request->update_color, $colorsCHeck)) {
            return redirect()->back()->with('failure', 'Color exists already');
        }

        $color = Color::findOrFail($request->update_color);

        ProductColorSize::where('product_id', $request->product_id)->where('color_id', $request->current_color)->update(['color_id' => $request->update_color]);
        return redirect()->back()->with('success', 'Color updated');
    }

    public function variationSizeEdit(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'size_id' => 'nullable',
            'size_details' => 'nullable',
            'price' => 'nullable',
        ]);

        if (!$validator->fails()) {
            if (empty($request->size_id)) {
                ProductColorSize::where('id', $request->id)->update([
                    'price' => $request->price,
                    'offer_price' => $request->price,
                ]);
            } else {
                // check if the size exists already
                $productColorSizeDetail = ProductColorSize::findOrFail($request->id);

                $check = ProductColorSize::where('product_id', $productColorSizeDetail->product_id)->where('color_id', $productColorSizeDetail->color_id)->where('size_id', $request->size_id)->count();

                if ($check == 0) {
                    $sizeName = Size::select('name')->where('id', $request->size_id)->first();

                    ProductColorSize::where('id', $request->id)->update([
                        'size_id' => $request->size_id,
                        'price' => $request->price,
                        'offer_price' => $request->price,
                    ]);
                } else {
                    return redirect()->back()->with('failure', 'This color & size already exist for this product. Select a different one.')->withInput($request->all());
                }
            }

            return redirect()->back()->with('success', 'Size details updated successfully');
        } else {
            return redirect()->back()->with('failure', $validator->errors()->first())->withInput($request->all());
        }
    }

    public function variationColorPosition(Request $request)
    {
        // dd($request->all());
        $position = $request->position;
        $i = 1;
        foreach ($position as $key => $value) {
            $banner = ProductColorSize::findOrFail($value);
            $banner->position = $i;
            $banner->save();
            $i++;
        }
        return response()->json(['status' => 200, 'message' => 'Position updated']);
    }

    public function variationStatusToggle(Request $request)
    {
        
        $data = ProductColorSize::where('product_id', $request->productId)->where('color_id', $request->colorId)->first();
        
        if ($data) {
            if ($data->status == 1) {
                $status = 0;
                $statusType = 'inactive';
                $statusMessage = 'Color is inactive';
            } else {
                $status = 1;
                $statusType = 'active';
                $statusMessage = 'Color is active';
            }

            $data->status = $status;
            $data->save();
            //dd($data);
            return response()->json(['status' => 200, 'type' => $statusType, 'message' => $statusMessage]);
        } else {
            return response()->json(['status' => 400, 'message' => 'Something happened']);
        }
    }

    public function variationFabricUpload(Request $request)
    {
        // dd($request->all());

        $save_location = 'public/uploads/color/';
        $data = $request->image;
        $image_array_1 = explode(";", $data);
        $image_array_2 = explode(",", $image_array_1[1]);
        $data = base64_decode($image_array_2[1]);
        $imageName = mt_rand() . '_' . time() . '.png';

        if (file_put_contents($save_location . $imageName, $data)) {
            // $user = Auth::user();
            // $user->image_path = $save_location.$imageName;
            // $user->save();
            // return response()->json(['error' => false, 'message' => 'Image updated', 'image' => asset($save_location.$imageName)]);

            $productVariation = ProductColorSize::where('product_id', $request->product_id)->where('color_id', $request->color_id)->get();

            foreach ($productVariation as $item) {
                $item->color_fabric = $save_location . $imageName;
                $item->save();
            }

            return response()->json(['error' => false, 'message' => 'Image uploaded', 'image' => asset($save_location . $imageName), 'color_id' => $request->color_id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Something went wrong']);
        }
    }

    public function variationCSVUpload(Request $request)
    {
        if (!empty($request->file)) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            $valid_extension = array("csv");
            $maxFileSize = 50097152;
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize <= $maxFileSize) {
                    $location = 'public/uploads/csv';
                    $file->move($location, $filename);
                    // $filepath = public_path($location . "/" . $filename);
                    $filepath = $location . "/" . $filename;

                    // dd($filepath);

                    $file = fopen($filepath, "r");
                    $importData_arr = array();
                    $i = 0;
                    while (($filedata = fgetcsv($file, 10000, ",")) !== FALSE) {
                        $num = count($filedata);
                        // Skip first row
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);
                    $successCount = 0;

                    foreach ($importData_arr as $importData) {
                        $insertData = array(
                            "PRODUCT_STYLE_NO" => isset($importData[0]) ? $importData[0] : null,
                            "COLOR_MASTER" => isset($importData[1]) ? $importData[1] : null,
                            "CUSTOM_COLOR_NAME" => isset($importData[2]) ? $importData[2] : null,
                            "SIZE" => isset($importData[3]) ? $importData[3] : null,
                            "PRICE" => isset($importData[4]) ? $importData[4] : null,
                            "OFFER_PRICE" => isset($importData[5]) ? $importData[5] : null,
                            "STOCK" => isset($importData[6]) ? $importData[6] : 1,
                            "SKU_CODE" => isset($importData[7]) ? $importData[7] : null,
                            "COLOR_POSITION" => isset($importData[8]) ? $importData[8] : 1,
                            "STATUS" => isset($importData[9]) ? $importData[9] : 1
                        );

                        $resp = ProductColorSize::insertData($insertData, $successCount);
                        $successCount = $resp['successCount'];
                    }

                    Session::flash('message', 'CSV Import Complete. Total no of entries: ' . count($importData_arr) . '. Successfull: ' . $successCount . ', Failed: ' . (count($importData_arr) - $successCount));
                } else {
                    Session::flash('message', 'File too large. File must be less than 50MB.');
                }
            } else {
                Session::flash('message', 'Invalid File Extension. supported extensions are ' . implode(', ', $valid_extension));
            }
        } else {
            Session::flash('message', 'No file found.');
        }

        return redirect()->back();
    }

}
