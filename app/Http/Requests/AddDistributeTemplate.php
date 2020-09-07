<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddDistributeTemplate extends FormRequest
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
            'price'     => 'required',
            'cost'      => 'required',
            'zero'      => 'required',
            'one'       => 'required',
            'sec'       => 'required',
            'three'     => 'required',
            'title'     => 'required',
        ];
    }
    public function message()
    {
        return [
            'price.required'     => '模拟售价不能为空',
            'cost.required'      => '分销成本不能为空',
            'zero.required'      => '本机佣金不能为空',
            'one.required'       => '一级佣金不能为空',
            'sec.required'       => '二级佣金不能为空',
            'three.required'     => '三级佣金不能为空',
            'title.required'     => '模板名称不能为空',
        ];
    }
    public function response(array $errors)
    {
        $error = current($errors);
        return myerror($error[0]);
    }
}
