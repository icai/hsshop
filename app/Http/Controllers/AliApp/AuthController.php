<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/7/19
 * Time: 13:58
 */

namespace App\Http\Controllers\AliApp;


use Illuminate\Http\Request;

class AuthController
{

    public function login(Request $request){
       show_debug($request->session()->all());
    }

}