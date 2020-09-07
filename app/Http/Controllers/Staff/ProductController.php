<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/4/27
 * Time: 10:06
 */

namespace App\Http\Controllers\Staff;


use App\Http\Controllers\Controller;
use App\S\Product\CategoryService as ProductCategoryService;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller
{

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170427
     * @desc 分类列表
     */
    public function category(ProductCategoryService $productCategoryService)
    {
        $category = $productCategoryService->getAll();
        $category[0]['data'] = $category;
        $category[1] = '';

        return view('staff.product.category',array(
            'title'          => '商品管理',
            'sliderba'      => 'category',
            'category'      => $category,
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc 删除份分类
     * @param ProductCategoryService $productCategoryService
     * @param $id
     */
    public function del(ProductCategoryService $productCategoryService,$id)
    {
        $productCategoryService->delete($id);
        success('','',$id);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc 添加商品品类
     * @param Request $request
     * @param ProductCategoryService $productCategoryService
     */
    public function add(Request $request,ProductCategoryService $productCategoryService)
    {
        $input = $request->input();
        $rule = Array(
            'category_name'    => 'required|max:12',
            'listorder'         => 'required|integer',
        );
        $message = Array(
            'category_name.required'     => '请填写分类名称',
            'listorder.required'          => '请填写分类名称',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $category = [
            'category_name' => $input['category_name'],
            'listorder'      => $input['listorder'],
        ];

        //是否属于其他的子类
        if (isset($input['is_other']) && $input['is_other']) {
            $category['parent_id'] = 8;
        }

        if (isset($input['id']) && $input['id']){
            $category['id'] = $input['id'];
            $res = $productCategoryService->update(['id'=>$input['id']],$category);
            $res?success('','',$category):error();
        }

        $id = $productCategoryService->add($category);
        success('','',$id);
    }

}