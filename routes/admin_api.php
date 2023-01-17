<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware'=>'check_guards:admin-api', 'prefix'=>'admin'], function(){
    Route::post('get-usage', 'AdminController@index')->name('dashboard');  //just for example
    Route::post('logout', 'AdminController@logout');
    Route::post('show-companies', 'AdminController@displayCompanies');
    Route::post('show-experts', 'AdminController@displayExperts');
    Route::post('add-admin-news', 'AdminController@addAdminFashionNews');
    Route::post('show-admin-news', 'AdminController@displayMyNews_Admin');
    Route::get('destroy-admin-news/{news_id}', 'AdminController@deleteMyNews_Admin');
    Route::post('add-admin', 'AdminController@addAnotherAdmin');
    ####################### Begin Blocks ###############################
    //block company
    Route::get('block-company/{company_id}', 'AdminController@blockcompany');
    //return company from block
    Route::get('back-company-from-block/{company_id}', 'AdminController@cancelBlockCompany');
    //block expert
    Route::get('block-expert/{expert_id}', 'AdminController@blockexpert');
    //return expert from block
    Route::get('back-expert-from-block/{expert_id}', 'AdminController@cancelBlockexpert');
    //get all blocked companies
    Route::post('get-blocked-companies', 'AdminController@displayAllBlockedCompanies');
    //getl all blocked experts
    Route::post('get-blocked-experts', 'AdminController@displayAllBlockedExperts');
    
    ####################### End Blocks ###############################
    ########################### Begin Admin statistics ###############
    //Count_Company
    Route::post('count-non-block-company', 'AdminController@CountNotBlockedCompany_Admin');
    //count blocked companies
    Route::post('count_blocked_company', 'AdminController@CountBlockedCompany');
    //Count_expert
    Route::post('count-non-blocked-expert', 'AdminController@CountNotBlockedExpert');
    //blocked experts
    Route::post('count_blocked_expert', 'AdminController@CountBlockedExpert');
    //Count_user
    Route::post('count_user', 'AdminController@CountUser');
    //Count_pieces
    Route::post('count_pieces', 'AdminController@CountPieces');

    ########################### End Admin statistics ###################
    ########################### Begin csv controles ####################
    //export csv file
    Route::post('export-to-csv/{seasonName}', 'AdminController@exportToCSV');
    Route::post('predict-color/{seasonName}', 'AdminController@createCSVFile');
    ########################### Begin Admin data ########################
    Route::get('get-admin-data', 'AdminController@getAdminData');
    ########################### End Admin data ########################
    ########################### Begin Email ###########################
    Route::get('send-email-to-expert', 'ExpertController@sendEmailToExpert');
    ########################## End email #############################

});

Route::group(['middleware'=>'api', 'prefix'=>'admin'], function(){
    Route::post('login', 'AdminController@login') ;
    Route::post('register', 'AdminController@register') ;
});