<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateRoleGroup extends FormRequest
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
           // 'title'=>'required|max:255|alpha_dash',
          //  'image'=>'mimes:jpeg,bmp,png,gif,jpg',
           // 'parent'=>'numeric',
           // 'description'=>'alpha_dash|max:1000'
        ];
    }
    public function messages()
    {
        return [
           // 'title.required'=>'Tiêu đề không được để trống',
           // 'title.max'=>'Tiêu đề tối đa 255 ký tự',
           // 'title.alpha_dash'=>'Tiêu đề không được chứa ký tự đặc biệt',
           // 'image.mines'=>'Ảnh sai định dạng',
           // 'parent.numeric'=>'category cha phải là số',
           // 'description.alpha_dash'=>'Mô tả chứa ký tự đặc biệt',
           // 'description.max'=>'Mô tả max 1000 ký tự'
        ];
    }
}
