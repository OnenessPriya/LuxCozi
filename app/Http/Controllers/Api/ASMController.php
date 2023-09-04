<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailerListOfOcc;
use App\Models\Activity;
use App\Models\Team;
use App\Models\User;
use App\Models\Store;
use App\Models\Cart;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class ASMController extends Controller
{
    //inactive ASE report for ASM in dashboard
    public function inactiveAseListASM(Request $request)
    {
        $useId = $_GET['user_id'];
        $aseDetails = Team::select('users.id')->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.asm_id', '=', $useId)->groupby('teams.ase_id')->orderby('teams.ase_id')->get()->pluck('id')->toArray();
                
        $activeASEreport=Activity::where('type','Visit Started')->whereDate('created_at', '=', Carbon::now())->whereIn('user_id',$aseDetails)->pluck('user_id')->toArray();
                
        $inactiveASE=Team::select(DB::raw("users.id as id"),DB::raw("users.name as name"),DB::raw("users.mobile as mobile"),DB::raw("users.state as state"),DB::raw("users.city as city"))->join('users', 'teams.ase_id', '=', 'users.id')->where('teams.asm_id', '=', $userId)->whereNotIn('users.id',$activeASEreport)->groupby('teams.ase_id')->orderby('teams.ase_id')->get();
            
        return response()->json(['error' => false, 'resp' => 'Inactive ASE report - Team wise', 'data' => $inactiveASE]);
        
    }

    //area list
    public function areaList(Request $request,$id)
    {
        $data=Team::where('asm_id',$id)->groupby('area_id')->with('areas:id,name')->get();
        if (count($data)==0) {
                 return response()->json(['error'=>true, 'resp'=>'No data found']);
        } else {
                 return response()->json(['error'=>false, 'resp'=>'Area List','data'=>$data]);
        } 
    }
    
    //distributor list
   public function distributorList(Request $request)
    {
        $asm = $_GET['user_id'];
        $area = $_GET['area_id'];
        $data= Team::select('distributor_id','area_id')->where('asm_id',$asm)->where('area_id',$area)->with('distributors:id,name,mobile,email,address,city,state')->distinct('distributor_id')->get();
        if($data)
        {
            return response()->json(['error' => false, 'resp' => 'Distributor data fetched successfully','data' => $data]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
   }

   //store image create
    public function imageCreate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => ['required', 'image', 'max:1000000']
        ]);

        if(!$validator->fails()){
            $imageName = mt_rand().'.'.$request->image->extension();
			$uploadPath = 'public/uploads/store';
			$request->image->move($uploadPath, $imageName);
			$total_path = $uploadPath.'/'.$imageName;
            
			return response()->json(['error' => false, 'resp' => 'Image added', 'data' => $total_path]);

        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }
    //store create
    public function storeCreate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|integer',
            'contact' => 'required|integer|unique:stores|min:1|digits:10',
            'whatsapp' => 'required|integer|unique:stores|min:1|digits:10',
            'name' => 'required',
            'distributor_id' => 'required',
            'owner_fname' => 'required|regex:/^[\pL\s\-]+$/u',
            'owner_lname' => 'required|regex:/^[\pL\s\-]+$/u',
            'image' => 'required',
            'contact_person_fname' => 'required|regex:/^[\pL\s\-]+$/u',
            'pin' => 'required|integer|digits:6',
           'contact_person_lname' => 'required|regex:/^[\pL\s\-]+$/u',
           'area_id' => 'required',
           'state_id' => 'required',
           'contact_person_phone' => 'required|integer|',
        ]);

        if(!$validator->fails()){
            $result = Team::where('asm_id',$request->user_id)->where('area_id',$request->area_id)->where('distributor_id',$request->distributor_id)->first();
            $user = User::where('id',$request->user_id)->first();
            $name = $user->name;
            $store=new Store();
            $store->user_id = $request->user_id;
			$store->name = $request->name;
			$store->business_name	 = $request->business_name ?? null;
			$store->owner_fname	 = $request->owner_fname ?? null;
		    $store->owner_lname	 = $request->owner_lname ?? null;
			$store->store_OCC_number = $request->store_OCC_number ?? null;
			$store->gst_no = $request->gst_no ?? null;
			$store->contact = $request->contact;
			$store->whatsapp = $request->whatsapp?? null;
			$store->email	 = $request->email?? null;
			$store->address	 = $request->address?? null;
			$store->state_id	 = $request->state_id?? null;
			$store->city	 = $request->area_id?? null;
			$store->pin	 = $request->pin?? null;
			$store->area_id	 = $request->area_id?? null;
			$store->date_of_birth	 = $request->date_of_birth?? null;
			$store->date_of_anniversary	 = $request->date_of_anniversary?? null;
			$store->contact_person_fname	 = $request->contact_person_fname ?? null;
	    	$store->contact_person_lname = $request->contact_person_lname ?? null;
			$store->contact_person_phone	= $request->contact_person_phone ?? null;
			$store->contact_person_whatsapp	 = $request->contact_person_whatsapp ?? null;
			$store->contact_person_date_of_birth	 = $request->contact_person_date_of_birth ?? null;
			$store->contact_person_date_of_anniversary	 = $request->contact_person_date_of_anniversary ?? null;
            $orderData = Store::select('sequence_no')->latest('sequence_no')->first();
				
				    if (empty($store->sequence_no)) {
						if (!empty($orderData->sequence_no)) {
							$new_sequence_no = (int) $orderData->sequence_no + 1;
							
						} else {
							$new_sequence_no = 1;
							
						}
					}
			$uniqueNo = sprintf("%'.06d", $new_sequence_no);
		    $store->sequence_no = $new_sequence_no;
			$store->unique_code = $uniqueNo;
			$store->status = '0';
			if (!empty($collection['image'])) {
				$store->image= $request->image;
			}
			
            $slug = Str::slug($request->name, '-');
            $slugExistCount = Store::where('slug', $slug)->count();
            if ($slugExistCount > 0) $slug = $slug.'-'.($slugExistCount+1);
            $store->slug = $slug;
			
			$store->created_at = date('Y-m-d H:i:s');
			$store->updated_at = date('Y-m-d H:i:s');
			$store->save();

			$nsm_id = $result->nsm_id;
			$state_id = $result->state_id;
			$zsm_id = $result->zsm_id;
			$rsm_id = $result->rsm_id;
            $ase_id = $result->ase_id;
            $sm_id = $result->sm_id;

			$team = new Team;
			$team->nsm_id = $nsm_id;
			$team->state_id = $state_id;
			$team->zsm_id = $zsm_id;
			$team->rsm_id = $rsm_id;
			$team->asm_id = $request->user_id;
			$team->sm_id = $sm_id;
			$team->ase_id = $ase_id;
			$team->area_id = $request->area_id;
            $team->distributor_id = $request->distributor_id;
			$team->store_id = $store->id;
			$team->status = '1';
			$team->is_deleted = '0';
			$team->created_at = date('Y-m-d H:i:s');
			$team->updated_at = date('Y-m-d H:i:s');
			$team->save();
			// notification to Admin
			$loggedInUser = $name;
				sendNotification($store->user_id, 'admin', 'store-add', 'admin.store.index', $store->name. '  added by ' .$loggedInUser , '  Store ' .$store->name.' added');
				
				// notification to RSM
				$loggedInUser = $name;
				$rsm = DB::select("SELECT u.id as rsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.rsm_id where t.ase_id = '$request->user_id' ");
				foreach($rsm as $value){
					sendNotification($store->user_id, $value->rsm_id, 'store-add', '', $store->name. '  added by '  .$loggedInUser ,' Store ' .$store->name. ' added');
				}

				// notification to SM
				$loggedInUser = $name;
				$sm = DB::select("SELECT u.id as sm_id FROM `teams` t  INNER JOIN users u ON u.id = t.sm_id where t.ase_id = '$request->user_id' ");
				foreach($sm as $value){
					sendNotification($store->user_id, $value->sm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
				}
                // notification to ZSM
				$loggedInUser = $name;
				$zsm = DB::select("SELECT u.id as zsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.zsm_id where t.ase_id = '$request->user_id'  ");
				foreach($zsm as $value){
					sendNotification($store->user_id, $value->zsm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
				}
                // notification to NSM
				$loggedInUser = $name;
				$nsm = DB::select("SELECT u.id as nsm_id FROM `teams` t  INNER JOIN users u ON u.id = t.nsm_id where t.ase_id = '$request->user_id'");
				foreach($nsm as $value){
					sendNotification($store->user_id, $value->nsm_id, 'store-add', '', $store->name. '  added by ' .$loggedInUser ,'Store ' .$store->name.' added  ');
				}
                return response()->json(['error'=>false, 'resp'=>'Store data created successfully','data'=>$store]);
        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }

    //store list
    public function storeList(Request $request)
    {
        
        $areaId = $_GET['area_id'];
        $stores =Store::where('area_id',$areaId)->where('status',1)->orderby('id','desc')->with('states:id,name','areas:id,name')->get();
        if ($stores) {
		    return response()->json(['error'=>false, 'resp'=>'Store data fetched successfully','data'=>$stores]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    //store search for ASM area wise
    public function searchStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'area_id' => 'required',
            'keyword' => 'required'
        ]);

        if(!$validator->fails()){
            $areaId = $_GET['area_id'];
            $search = $_GET['keyword'];
            $data = Store::select('*');
            
            if(!empty($search)){
                $data = $data->where('status',1)->where('area_id',$areaId)->where('contact', '=',$search)->orWhere('name', 'like', '%'.$search.'%')->with('states:id,name','areas:id,name');
            }        

            $data = $data->get();
            if(!empty($data)){
                foreach($data as $item){
                    $retailer=Team::where('store_id',$item->id)->with('distributors:id,name')->first();
                    $item->team = $retailer;
                }
            }
            return response()->json([
                'error'=>false,
                'resp'=>"Store List",
                'data'=> $data
                
            ]);
        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }

    }

    //inactive store list user wise
    public function inactiveStorelist(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'area_id' => 'required'
        ]);
        if(!$validator->fails()){
            $ase = $_GET['user_id'];
            $area = $_GET['area_id'];
            $stores = Store::where('user_id',$ase)->where('area_id',$area)->where('status',0)->get();
            if ($stores) {
                return response()->json(['error'=>false, 'resp'=>'Store data fetched successfully','data'=>$stores]);
            } else {
                return response()->json(['error' => true, 'resp' => 'Something happened']);
            }
        }else {
                return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
            }  
    }

    //cart list user wise
    public function cartList($id,$userId)
    {
        $cart=Cart::where('store_id',$id)->where('user_id',$userId)->with('product:id,name,style_no','color:id,name','size:id,name')->get();
        $cart_count = DB::select("select ifnull(sum(qty),0) as total_qty from carts where store_id='$id' and user_id='$userId'");
            
        if(count($cart_count)>0){
            $total_quantity = $cart_count[0]->total_qty;
        }else{
            $total_quantity = 0;
        }
        if ($cart) {
            return response()->json(['error'=>false, 'resp'=>'cart List fetched successfully','data'=>$cart,'total_quantity'=>$total_quantity]);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }

    //add to cart
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'store_id' => 'required',
            'product_id' => 'required',
            'order_type' => 'required',
            'color' => 'required'
        ]);
        if(!$validator->fails()){
            $collectedData = $request->except('_token');
            $multiColorSizeQty = explode("|", $collectedData['color']);
            $colors = array();
            $sizes = array();
            $qtys = array();
            $multiPrice =array();
            foreach($multiColorSizeQty as $m){
                $str_arr = explode("*",$m);
                array_push($colors,$str_arr[0]);
                array_push($sizes,$str_arr[1]);
                array_push($qtys,$str_arr[2]);
                
            }

            for($i=0;$i<count($colors);$i++)
            {
                $cartExists = Cart::where('product_id', $collectedData['product_id'])->where('user_id', $collectedData['user_id'])->where('color_id', $colors[$i])->where('size_id', $sizes[$i])->first();
                
    
                if ($cartExists) {
                        $cartExists->qty = $cartExists->qty + $qtys[$i];
                        $cartExists->save();
                } else {
                    if ($collectedData['order_type']) {
                        if ($collectedData['order_type'] == 'store-visit') {
                            $orderType = 'Store visit';
                        } else {
                            $orderType = 'Order on call';
                        }
                    } else {
                        $orderType = null;
                    }
                    
                    $newEntry = new Cart;
                    $newEntry->user_id = $collectedData['user_id'];
                    $newEntry->store_id = $collectedData['store_id'] ?? null;
                    $newEntry->order_type = $orderType;
                    $newEntry->product_id = $collectedData['product_id'];
                    $newEntry->color_id = $colors[$i];
                    $newEntry->size_id = $sizes[$i];
                    $newEntry->qty = $qtys[$i];

                    $newEntry->save();
                }
            }
            return response()->json(['error'=>false, 'resp'=>'Product added to cart successfully','data'=>$newEntry]);
        }else {
            return response()->json(['error' => true, 'resp' => $validator->errors()->first()]);
        }
    }

    //cart quantity update
    public function cartUpdate(Request $request, $cartId,$q)
    {
        $cart = Cart::findOrFail($cartId);

        if ($cart) {
			 $cart->qty = $q;
			 $cart->save();
            return response()->json([
                'error' => false,
                'resp' => 'Quantity updated'
            ]);
        } else {
            return response()->json([
                'error' => true,
                'resp' => 'Something Happened'
            ]);
        }
    }
    //cart delete
    public function destroy($id)
    {
        $cart=Cart::destroy($id);
        if ($cart) {
            return response()->json(['error'=>false, 'resp'=>'Product removed from cart']);
        } else {
            return response()->json(['error' => true, 'resp' => 'Something happened']);
        }
    }
    //cart preview url
    public function PDF_URL(Request $request, $id,$userId)
    {
        return response()->json([
            'error' => false,
            'resp' => 'URL generated',
            'data' => url('/').'/api/cart/pdf/view/'.$id.'/'.$userId,
        ]);
    }

    
    //cart preview
    public function PDF_view(Request $request, $id,$userId)
    {
        $cartData =Cart::where('store_id',$id)->where('user_id',$userId)->with('product','stores','color','size')->get()->toArray();
		
        return view('api.cart-pdf', compact('cartData'));
    }

}
