<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\State;
use App\Models\Area;
use App\Models\User;
use App\Models\Team;
use App\Models\UserArea;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
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
        $data->state_id = $collection['cat_id'] ?? '';
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
    
    //user csv upload
     public function userCSVUpload(Request $request)
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
                        $stateData = '';
                        foreach (explode(',', $importData[3]) as $cateKey => $catVal) {
                            $catExistCheck = State::where('name', $catVal)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $stateData = $insertDirCatId;
                            } else {
                                $dirCat = new State();
                                $dirCat->name = $catVal;
                                $dirCat->status = 1;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $stateData = $insertDirCatId;
                            }
                        }
                        $areaData = '';
                        foreach (explode(',', $importData[2]) as $cateKey => $catVal) {
                            $catExistCheck = Area::where('name', $catVal)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $areaData = $insertDirCatId;
                            } else {
                                $dirCat = new Area();
                                $dirCat->name = $catVal;
                                $dirCat->state_id = $stateData;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $areaData = $insertDirCatId;
                            }
                        }
                        $nsmData = '';
                        foreach (explode(',', $importData[4]) as $cateKey => $catVal) {
                            $catExistCheck = User::where('name', $catVal)->where('type',1)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $nsmData = $insertDirCatId;
                            } else {
                                $dirCat = new User();
                                $dirCat->name = $catVal;
                                $dirCat->type = 1;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $nsmData = $insertDirCatId;
                            }
                        }
                        $zsmData = '';
                        foreach (explode(',', $importData[5]) as $cateKey => $catVal) {
                            $catExistCheck = User::where('name', $catVal)->where('type',2)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $zsmData = $insertDirCatId;
                            } else {
                                $dirCat = new User();
                                $dirCat->name = $catVal;
                                $dirCat->type = 2;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $zsmData = $insertDirCatId;
                            }
                        }
                        $rsmData = '';
                        foreach (explode(',', $importData[6]) as $cateKey => $catVal) {
                            $catExistCheck = User::where('name', $catVal)->where('type',3)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $rsmData = $insertDirCatId;
                            } else {
                                $dirCat = new User();
                                $dirCat->name = $catVal;
                                $dirCat->type = 3;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $rsmData = $insertDirCatId;
                            }
                        }
                        $smData = '';
                        foreach (explode(',', $importData[7]) as $cateKey => $catVal) {
                            $catExistCheck = User::where('name', $catVal)->where('type',4)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $smData = $insertDirCatId;
                            } else {
                                $dirCat = new User();
                                $dirCat->name = $catVal;
                                $dirCat->type = 4;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $smData = $insertDirCatId;
                            }
                        }
                        $asmData = '';
                        foreach (explode(',', $importData[8]) as $cateKey => $catVal) {
                            $catExistCheck = User::where('name', $catVal)->where('type',5)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $asmData = $insertDirCatId;
                            } else {
                                $dirCat = new User();
                                $dirCat->name = $catVal;
                                $dirCat->type = 5;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $asmData = $insertDirCatId;
                            }
                        }
                        $aseData = '';
                        foreach (explode(',', $importData[9]) as $cateKey => $catVal) {
                            $catExistCheck = User::where('name', $catVal)->where('type',6)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $aseData = $insertDirCatId;
                            } else {
                                $dirCat = new User();
                                $dirCat->name = $catVal;
                                $dirCat->type = 6;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $aseData = $insertDirCatId;
                            }
                        }
                         $nameData= explode(' ', $importData[0]);
                         if(!empty($nameData[5])){
                             $lname=$nameData[1].' '.$nameData[2].' '.$nameData[3].' '.$nameData[4].' '.$nameData[5];
                         }
                         if(!empty($nameData[4])){
                             $lname=$nameData[1].' '.$nameData[2].' '.$nameData[3].' '.$nameData[4];
                         }
                         if(!empty($nameData[3])){
                             $lname=$nameData[1].' '.$nameData[2].' '.$nameData[3];
                         }
                          elseif(!empty($nameData[2])){
                             $lname=$nameData[1].' '.$nameData[2];
                         }
                         elseif(empty($nameData[1])){
                             $lname='';
                         }
                         else{
                             $lname=$nameData[1];
                         }
                         $password = Hash::make($nameData[0].$importData[1]);
                         $insertData = array(
                             "name" => isset($importData[0]) ? $importData[0] : null,
                             "fname" => isset($nameData[0]) ? $nameData[0] : null,
                             "lname" => $lname,
                             "designation" => 'Distributor',
                             "employee_id" => isset($importData[1]) ? $importData[1] : null,
                             "state" => isset($importData[3]) ? $importData[3] : null,
                             "city" => isset($importData[2]) ? $importData[2] : null,
                             "password" => $password,
                             "type" => 7,
                             "status" => 1
                         );
 
                        $resp = User::insertData($insertData, $successCount);
                        $successCount = $resp['successCount'];
                        $userId = $resp['id'];
                        $store = new Team;
                        $store->state_id =  $stateData;
                        $store->area_id = $areaData;
                        $store->distributor_id = $userId;
                        $store->nsm_id = $nsmData;
                        $store->zsm_id = $zsmData;
                        $store->rsm_id = $rsmData;
                        $store->asm_id = $asmData;
                        $store->sm_id = $smData;
                        $store->ase_id = $aseData;
                        $store->status = 1;
                        $store->save();
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
                       
                        $areaData = '';
                        foreach (explode(',', $importData[1]) as $cateKey => $catVal) {
                            $catExistCheck = Area::where('name', $catVal)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $areaData = $insertDirCatId;
                            } else {
                                $dirCat = new Area();
                                $dirCat->name = $catVal;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $areaData = $insertDirCatId;
                            }
                        }
                        
                        $aseData = '';
                        foreach (explode(',', $importData[0]) as $cateKey => $catVal) {
                            $catExistCheck = User::where('name', $catVal)->where('type',6)->first();
                            if ($catExistCheck) {
                                $insertDirCatId = $catExistCheck->id;
                                $aseData = $insertDirCatId;
                            } else {
                                $dirCat = new User();
                                $dirCat->name = $catVal;
                                $dirCat->type = 6;
                                $dirCat->save();
                                $insertDirCatId = $dirCat->id;

                                $aseData = $insertDirCatId;
                            }
                        }
                         
                         $insertData = array(
                             "user_id" => isset($aseData) ? $aseData : null,
                             "area_id" => isset($areaData) ? $areaData : null,
                             "created_at" => date('Y-m-d H:i:s'),
                             "updated_at" => date('Y-m-d H:i:s'),
                         );
 
                        $resp = UserArea::insertGetId($insertData);
                        $successCount = $successCount++;
                       
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
