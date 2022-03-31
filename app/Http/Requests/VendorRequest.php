<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logo' => 'required_without:vend_id|mimes:jpg,jpeg,png',
            'name' => 'required_without:vend_id|string|max:150',
            'mobile' => 'required_without:vend_id|max:100|unique:vendors,mobile,' . $this-> id,       // must be exist in vendors table in the database in the mobile column so that the hacker don't get me from this field and concatinate the id to the unique to tell the validation that don't have to change it during the update methods
            'email' => 'required_without:vend_id|email|unique:vendors,email,' . $this-> id,           // must be exist in vendors table in the database in the email column so that the hacker don't get me from this field
            'category_id' => 'required_without:vend_id|exists:main_categories,id', // must be exist in main_categories table in the database so that the hacker don't get me from this field
            'address' => 'required_without:vend_id|string|max:500',
            'password' => 'required_without:vend_id',
        ];
    }

    public function messages() {
        return [
            'required' => "هذا الحقل مطلوب",
            'max' => 'هذا الحقل طويل',
            'string' => 'لابد ان يكون حروف او حروف وارقام',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'category_id.exists' => 'هذا القسم غير موجود ',
            'logo.required_without' => 'الصورة مطلوبة',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل ',
            'mobile.unique' => 'هذا الهاتف مستخدم من قبل ',
            'min' => 'قيمة الحقل قليلة ',

        ];
    }
}
