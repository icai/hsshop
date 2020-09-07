<?php
// @author 陈文豪 2020年07月03日20:01:32
Route::group(['namespace' => 'Java\V1', 'prefix' => 'java', 'middleware' => ['java']], function() {
    Route::get('rsyncCrm', 'MerchantController@rsyncCrm'); // 同步CRM
});
