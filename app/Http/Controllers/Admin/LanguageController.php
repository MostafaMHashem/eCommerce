<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * show all languages 
     */
    public function index () {
        $languages = Language::select()->paginate(PAGINATION_COUNT);
        return view('admin.languages.index', compact('languages'));
    }

    /**
     * show the form to create new language 
     */
    public function create () {
        return view('admin.languages.create');
    }

    /**
     * store or save the fields from the form to the database 
     */
    public function store (LanguageRequest $request) {
        // validation
        // we make the request to control the validation 
        // and we make validation so that the hackers don't inject malware or sql injection

        // insert to database table
        try {
            $language = Language::create($request->except('_token'));
            if(!$request->active){
                $language->active = 0;
                $language->save();
            }
            return redirect()->route('admin.languages')->with(['success' => ' تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => ' هنالك خطأ يرجي المحاولة لاحقا']);
            
        }
    }

    /**
     * show the form to edit language that was saved before
     */
    public function edit($id) {
        $language = Language::select()->find($id);
        if(!$language) {
            return redirect()-> route('admin.languages') -> with(['error' => 'هذى اللغة غير موجوة']);
        }
        return view('admin.languages.edit', compact('language'));
    }

    /**
     * update the request from the form to the database
     */
    public function update( LanguageRequest $request ,$id) {
        try {
            // check if exists
            $language = Language::find($id);
            if(!$language) {
                return redirect()-> route('admin.languages.edit') -> with(['error' => 'هذى اللغة غير موجوة']);
            }

            //  validate
    
            // update 
            if(!$request -> has('active')) {
                $request -> request -> add(['active' => 0]);
            }
            $language -> update ($request -> except('_token'));
            return redirect()->route('admin.languages')->with(['success' => ' تم الحفظ بنجاح']);

            
        } catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => ' هنالك خطأ يرجي المحاولة لاحقا']);
            
        }
    }

    /**
     * delete the record permentaly
     */
    public function destroy ($id) {
        try {
            // check if exists
            $language = Language::find($id);
            if(!$language) {
                return redirect()-> route('admin.languages') -> with(['error' => 'هذى اللغة غير موجوة']);
            }

            //  validate
    
            // delete 
            $language -> delete();
            return redirect()->route('admin.languages')->with(['success' => ' تم الحذف  بنجاح']);

            
        } catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => ' هنالك خطأ يرجي المحاولة لاحقا']);
            
        }
    }
    
}
