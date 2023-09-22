<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\State;
use App\Models\Area;
class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!empty($request->term)) 
        {
            $data=Area::where('name',$request->term)->latest('id')->with('states')->paginate(30);
        }else{
            $data=Area::latest('id')->with('states')->paginate(30);
            
        }
        return view('admin.area.index', compact('data','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $states=State::orderby('name')->get();
        return view('admin.area.create',compact('states','request'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "state_id" => "required",
            
        ]);
        $collection = $request->except('_token');
        $data = new Area;
        $data->name = $collection['name'];
        $data->state_id = $collection['state_id'];
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.areas.index');
        } else {
            return redirect()->route('admin.areas.create')->withInput($request->all());
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
        $data=Area::where('id',$id)->first();
        return view('admin.area.detail',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=Area::findOrfail($id);
        $states=State::orderby('name')->get();
        return view('admin.area.edit',compact('data','states'));
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
            "name" => "required|string|max:255",
            
        ]);
        $collection = $request->except('_token');
        $data =  Area::findOrfail($id);
        $data->name = $collection['name'];
        $data->state_id = $collection['state_id'];
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.areas.index');
        } else {
            return redirect()->route('admin.areas.create')->withInput($request->all());
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
        $data=Area::destroy($id);
        if ($data) {
            return redirect()->route('admin.areas.index');
        } else {
            return redirect()->route('admin.areas.index')->withInput($request->all());
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
        $data = Area::findOrFail($id);
        $status = ( $data->status == 1 ) ? 0 : 1;
        $data->status = $status;
        $data->save();
        if ($data) {
            return redirect()->route('admin.areas.index');
        } else {
            return redirect()->route('admin.areas.create')->withInput($request->all());
        }
    }

     //area csv upload
     public function areaCSVUpload(Request $request)
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
                        $count = $total = 0;
                        $commaSeperatedCats = '';
                        foreach ($importData[1] as $cateKey => $catVal) {
                            $catExistCheck = State::where('name', $catVal)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $commaSeperatedCats = $insertDirCatId;
                            } else {
                                $dirCat = new State();
                                $dirCat->name = $catVal;
                                $dirCat->status = 1;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $commaSeperatedCats = $insertDirCatId;
                            }
                        }
                         $insertData = array(
                             "name" => isset($importData[0]) ? $importData[0] : null,
                             "state" => isset($commaSeperatedCats) ? $commaSeperatedCats : null,
                             "STATUS" => 1
                         );
 
                         $resp = Area::insertData($insertData, $successCount);
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
