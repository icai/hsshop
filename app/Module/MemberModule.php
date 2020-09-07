<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/1
 * Time: 10:36
 */

namespace App\Module;
use App\Model\MemberCheck;
use App\S\Member\MemberService;
use App\S\Member\UnifiedMemberService;

class MemberModule
{
    public function memberCheck($wid, $wxId)
    {
        if (empty($wxId)) {
            return true;
        }
        if (empty($wid)) {
        	return false;
        }
        $insetData['wid'] = $wid;
        $insetData['wx_id'] = $wxId;
        $id = MemberCheck::insertGetId($insetData);
        if ($id) {
        	return true;
        }
        return false;
    }

    /**
     * 获取用户手机号
     * @param $mid int 用户ID
     * @return string 手机号 | ''
     * @author Herry
     */
    public function getMemberPhoneByID($mid)
    {
        $phone = '';

        //先从member取
        $member = (new MemberService())->model->find($mid);
        if (!empty($member)) {
            $phone = $member->mobile;

            //不存在 则再从unified_member取
            if (empty($phone)) {
                $unified_member = (new UnifiedMemberService())->model->find($member->umid);
                $unified_member && $phone = $unified_member->mobile;
            }
        }

        return $phone;
    }
}