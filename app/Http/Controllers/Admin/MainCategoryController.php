<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\Main_Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
// use Str;

use Illuminate\Http\Request;


class MainCategoryController extends Controller
{
    /**
     * show all Main Categories with default language 
     */
    public function index() {
        $default_language = get_default_lang();
        $main_categories = Main_Category::where('translation_lang',$default_language)
                                            -> selection() 
                                            -> get();
        return view('admin.mainCategories.index',compact('main_categories'));
    }

    /**
     * show the form to create new language 
     */
    public function create () {
        return view('admin.mainCategories.create');
    }

    /**
     * store or save the fields from the form to the database 
     */
    public function store(MainCategoryRequest $request) {

        try {
            // return $request 
    
            // save photo
            $filePath = '';
            if($request -> has('photo')) {
                $filePath = uploadImage('main_categories', $request -> photo);
            }
            // part 1
            // 1- get all categories
            $main_categories = collect($request -> category);                   // we transform the response into collection 
            // 2- filter the result to get arabic language
            $filtered = $main_categories -> filter(function ($value, $key) {    // then filter it cause it return an array
                return $value['abbr'] == get_default_lang();                     // and we want a specific field from that array , بالبلدي عايز افصل اللغة العربية عن باقي اللغات
            });
            $default_category = array_values( $filtered -> all() )[0];          // then transform it into array 
            /* 
            * 3- create record in the database in arabic language  get its id 
            * Then 
            * put the code between Transaction and commit , 
            * cause we have multiple insert statement in the same function
            */

            /**
             * part 3
             *  put the code between Transaction , commit and rollback  , 
             * cause we have multiple insert statement in the same function and they all depend on eachother
             * 
            */ 

            DB::beginTransaction();

            $default_category_id = Main_Category::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'photo' => $filePath,
            ]);
            // part 2
            // 4- filter the record to get the rest except the arabic
            $categories = $main_categories -> filter(function ($value, $key) {    
                return $value['abbr'] != get_default_lang();                      //   عايز باقي اللغات عدا اللغة العربية    
            });
            /* 5- save the categories that are in other languages except the arabic ,
            * cause we saved in arabic once and get its id => $default_category_id
             * 
             */
            if(isset($categories) && $categories->count()) {
                $categories_arr = [];
                foreach ($categories as $category) {
                    $categories_arr[] = [
                        'translation_lang' => $category['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $category['name'],
                        'slug' => $category['name'],
                        'photo' => $filePath,
                    ];
                }
                // 6- insert the field or fields depends on the language into the database 
                Main_Category::insert($categories_arr);
            }
            DB::commit();

            return redirect() -> route('admin.maincategories') -> with(['success' => 'تم الحفظ بنجاح']);
            
        } catch (\Exception $ex) {

            DB::rollBack();
            return redirect() -> route('admin.maincategories') -> with(['error' => 'حدث خطأ ما اثناء التخزين في قاعدة البيانات برجاء المحاولة لاحقا']);
            // remember to change the message and don't put any details in it for more security

        }
    }

    /**
     * show the form to edit the main category
     */
    public function edit($mainCat_id) {
        // get specific main category and its translations
        $mainCategory = Main_Category::with('main_categories_rel')      // pass the relation 
                        -> selection()          // remember to add the foreign key to the selection
                        -> find($mainCat_id);  
        if(!$mainCategory) {
            return redirect() -> route('admin.maincategories') -> with(['error' => 'هذا القسم غير موجود ']);
        }
        return view('admin.mainCategories.edit',compact('mainCategory'));


    }

    /**
     * update the record in the database
     */
    public function update(MainCategoryRequest $request, $mainCat_id) {
        
        try {
            // validate
    
            // find the category with id 
            $mainCategory = Main_Category::find($mainCat_id);
            if(!$mainCategory) {
                return redirect() -> route('admin.maincategories') -> with(['error' => 'هذا القسم غير موجود ']);
            }
            
            // update
                // 1- the request is an array , so we get the first element
            $category = array_values($request -> category) [0];
            
            
            DB::beginTransaction();

            // 2- update active
            if(!$request -> has('category.0.active')) {
                $request -> request -> add(['active' => 0]);
            } else {
                $request -> request -> add(['active' => 1]);
            }
            // 3-  update image
            $filePath = $mainCategory -> photo;
            if($request -> has('photo')) {
                $filePath = uploadImage('main_categories', $request -> photo);
                Main_Category::where('id', $mainCat_id )-> update([
                    'photo' => $filePath,
                ]);
            }

            Main_Category::where('id', $mainCat_id )-> update([
                'name' => $category['name'],
                'active' => $request -> active,
                
            ]);

            DB::commit();

            return redirect() -> route('admin.maincategories') -> with(['success' => 'تم التحديث بنجاح']);
            
        } catch (\Exception $ex) {
            DB::rollBack();

            return redirect() -> route('admin.maincategories') -> with(['error' => 'حدث خطأ ما اثناء التخزين في قاعدة البيانات برجاء المحاولة لاحقا']);
        }


    }

    /**
     * delete the category if it doesn't have any  vendor enrolled in it
     * delete the translations categories as well , with the relation we make in the model
     *
     *  delete the translation categories using the observer MainCategoryObserver::class
     * */
    public function destroy($mainCat_id) {
        try {
            $main_category = Main_Category::find($mainCat_id);
            if(!$main_category) {
                return redirect() -> route('admin.maincategories') -> with(['error' => 'هذا القسم غير موجود ']);
            }
            $vendors = $main_category -> vendors();
            if(isset($vendors) && $vendors -> count() > 0) {
                return redirect() -> route('admin.maincategories') -> with(['error' => 'لا يمكن حذف هذا القسم  ']);
            }
    
            // delete the image from the containing folder
            $image = Str::after($main_category-> photo , 'assets');  // get the string name after 'assets' in --->  http://localhost/ecommerce/assets
            $image = base_path('assets' . $image);                   // then append the image name to the path in your computer or the server , and we add 'assets' to the image path
            unlink($image);                                          // delete the image form the containing folder
            
            
            $main_category -> delete();
    
            return redirect() -> route('admin.maincategories') -> with(['success' => ' تم حذف القسم بنجاح  ']);
            //code...
        } catch (\Exception $ex) {
            return redirect() -> route('admin.maincategories') -> with(['error' => 'حدث خطأ ما اثناء التخزين في قاعدة البيانات برجاء المحاولة لاحقا']);
        }

    }

    /**
     * change the active columns if active == 0 then deactivate all the vendors in the category
     *                           if active == 1 then activate the vendors in the category
     *   and update the vendors 'active' column using the observer MainCategoryObserver::class with the vendors() relation we made in the Main_Category model
     */
    public function changeStatus($mainCat_id) {
        try {
            $main_Category = Main_Category::find($mainCat_id);
            if(!$main_Category) {
                return redirect() -> route('admin.maincategories') -> with(['error' => 'هذا القسم غير موجود ']);
            }
    
    
            $status = $main_Category -> active == 0 ? 1 : 0;
            $main_Category -> update(['active' => $status]);
            return redirect() -> route('admin.maincategories') -> with(['success' => ' تم تحديث حالة القسم بنجاح  ']);
            
        } catch (\Exception $ex) {
            return redirect() -> route('admin.maincategories') -> with(['error' => 'حدث خطأ ما اثناء التخزين في قاعدة البيانات برجاء المحاولة لاحقا']);

        }

    }

}
