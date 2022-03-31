<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\Main_Category;
use App\Models\Vendor;
use App\Notifications\VendorCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;


class VendorsController extends Controller
{
    // show all vendors 
    public function index() {
        $vendors = Vendor::selection()->paginate(PAGINATION_COUNT);
        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * show the form to create new language 
     */
    public function create () {
        $categories = Main_Category::where('translation_of', 0) -> active() -> get();
        return view('admin.vendors.create', compact('categories'));
    }

    /**
     * store or save the fields from the form to the database 
     */
    public function store(VendorRequest $request) {
        
        try {
            // save photo
            $filePath = '';
            if($request -> has('logo')) {
                $filePath = uploadImage('vendors', $request -> logo);
            }
            // add the active to the request
            if(!$request -> has('active')) {
                $request -> request -> add(['active' => 0]);
            } else {
                $request -> request -> add(['active' => 1]);
            }
            $vendor = Vendor::create([
                'name' => $request -> name,
                'email' => $request -> email,
                'password' => $request -> password,
                'mobile' => $request -> mobile,
                'address' => $request -> address,
                'active' => $request -> active,
                'logo' => $filePath,
                'category_id' => $request -> category_id,
            ]);
            /**
             * TODO 
             * 1- add notification email
             * 2-learn how to send email using laravel 
             * 
             * below line for sending notification email for the vendor using mailtrap 
             * but still need to learn this concept   
             * 
             * Notification::send($vendor, new VendorCreated($vendor));    // what is supposed the first argument is sendto , the second argument is sendfrom
             * 
             * */ 
            return redirect()->route('admin.vendors')->with(['success' => 'تم الحفظ بنجاح']);
                
            
        } catch (\Exception $ex) {
            return redirect()->route('admin.vendors')->with(['error' => 'هناك خطأ برجاء المحاولة لاحفا ']);
        }

        
    }

    /**
     * show the form to edit the main category
     */
    public function edit($vend_id) {

        try {
            //code...
            $vendor = Vendor::selection() -> find($vend_id);
            if(!$vendor) {
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوف ']);
            }
            $categories = Main_Category::where('translation_of', 0) -> active() -> get();
            return view('admin.vendors.edit',compact('vendor', 'categories'));
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.vendors')->with(['error' => 'هناك خطأ برجاء المحاولة لاحفا ']);
        }


    }

    /**
     * update the record in the database
     */
    public function update(VendorRequest $request, $id) {
        
        try {
            
            $vend_id = $id;
    
            // find the vendor with id , it's a check step
            $vendor = Vendor::selection() -> find($vend_id);
            if(!$vendor) {
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوف ']);
            }
            
            // update
                
            
            DB::beginTransaction();
            
            // 1- update active 
            if(!$request -> has('active')) {
                $request -> request -> add(['active' => 0]);
            } else {
                $request -> request -> add(['active' => 1]);
            }
            // 2-  update logo in the request 
            $filePath = $vendor -> logo;
            if($request -> has('logo')) {
                $filePath = uploadImage('vendors', $request -> logo);
                Vendor::where('id', $vend_id )-> update([
                    'logo' => $filePath,
                ]);
            }
            // 3- update the rest fields
            /* Vendor::where('id', $vend_id )-> update([
                'name' => $request -> name,
                'email' => $request -> email,
                'password' => $request -> password,
                'address' => $request -> address,
                'mobile' => $request -> mobile,
                'active' => $request -> active,
                'category_id' => $request -> category_id,
                
            ]);
            */
                // OR ,but add the password like we made the active add it to the request
            $data = $request -> except('_token','logo','password','vend_id');   // all values in the request
            
            if ($request->has('password') && !is_null($request->  password)) {

                $data['password'] = bcrypt($request->password);
            }
        
            Vendor::where('id', $vend_id )-> update($data);
            DB::commit();
            // 4- redirect
            return redirect() -> route('admin.vendors') -> with(['success' => 'تم التحديث بنجاح']);
            
        } catch (\Exception $ex) {
            // return $ex;
            DB::rollback();
            return redirect() -> route('admin.vendors') -> with(['error' => 'حدث خطأ ما اثناء التخزين في قاعدة البيانات برجاء المحاولة لاحقا']);
        }


    }

    /**
     * delete the vendor 
     */

    public function destroy($vend_id) {
        try {
            $vendor = Vendor::find($vend_id);
            if(!$vendor) {
                return redirect() -> route('admin.vendors') -> with(['error' => 'هذا القسم غير موجود ']);
            }

            /**
             * 
             * 
             * when add products and sections to the vendor 
             * we'll make this check step
             * $vendors = $vendor -> vendors();
             * if(isset($vendors) && $vendors -> count() > 0) {
             *      return redirect() -> route('admin.vendors') -> with(['error' => 'لا يمكن حذف هذا القسم  ']);
             * }
             * 
             */

            

            // delete the logo from the containing folder
            
            $image = Str::after($vendor-> logo , 'assets');  // get the string name after 'assets' in --->  http://localhost/ecommerce/assets
            $image = base_path('assets' . $image);                   // then append the image name to the path in your computer or the server , and we add 'assets' to the image path
            unlink($image);                                          // delete the image form the containing folder
            
            
            $vendor -> delete();
    
            return redirect() -> route('admin.vendors') -> with(['success' => ' تم حذف المتجر بنجاح  ']);
            //code...
        } catch (\Exception $ex) {
            return redirect() -> route('admin.vendors') -> with(['error' => 'حدث خطأ ما اثناء التخزين في قاعدة البيانات برجاء المحاولة لاحقا']);
        }

    }

    /**
     * change the active columns if active == 0 then deactivate  the vendors 
     *                           if active == 1 then activate the vendors 
     *   
     */
    public function changeStatus($vend_id) {
        try {
            $vendor = Vendor::find($vend_id);
            if(!$vendor) {
                return redirect() -> route('admin.vendors') -> with(['error' => 'هذا المتجر غير موجود ']);
            }
    
    
            $status = $vendor -> active == 0 ? 1 : 0;
            $vendor -> update(['active' => $status]);
            return redirect() -> route('admin.vendors') -> with(['success' => ' تم تحديث حالة المتجر بنجاح  ']);
            
        } catch (\Exception $ex) {
            return redirect() -> route('admin.vendors') -> with(['error' => 'حدث خطأ ما اثناء التخزين في قاعدة البيانات برجاء المحاولة لاحقا']);

        }

    }
}
