<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//////////////////// Constants /////////////////////////////
define('NUM_PIECES_COMPANY_PROFILE', 10);
define('NUM_COLLECTION_COMPANY_PROFILE', 10);
define('NUM_NEWS_COMPANY_PROFILE', 10);
define('NUM_FORMS_COMPANY_PROFILE', 10);
/////////////////////////////////////////////////////////

Route::group(['middleware'=>'check_guards:company-api', 'prefix'=>'company'], function(){
    Route::post('get-usage', 'CompanyController@index'); 
    //company profile 
    Route::post('company-profile', 'CompanyController@displayCompanyProfile');
    //form edit form
    Route::post('edit-company-profile', 'CompanyController@editCompanyProfile');
    //save edit form
    Route::post('update-company-profile', 'CompanyController@updateCompanyProfile');
    //add fashion news
    Route::post('add-company-fashion-news', 'CompanyController@addFashionNews');
   //show all my news
   Route::post('show-my-fashion-news', 'CompanyController@displayMyNewsCompany');
   //delete my news
   Route::get('delete_my_news/{news_id}', 'CompanyController@deleteMyNewscompany');
    //logout
    Route::post('logout', 'CompanyController@logout');
    //get sub master usage color sizeseason for add piece
    

    //add piece form
    Route::post('add-piece-form', 'CompanyController@addPiece');
    

    //display all the piecesDetails
    Route::get('display-detail-pieces/{id}', 'CompanyController@displayDetailedPiece');

    //display all pieces
    Route::get('display-my-pieces', 'CompanyController@displayPieces');

    //add details to an existing piece
    Route::post('add-details-to-piece/{pieces_id}', 'CompanyController@addDetailsPiece');
    
    //edit piece
    Route::get('edit-piece/{piece_id}', 'CompanyController@editPiece');
    //update piece
    Route::post('update-piece/{piece_id}', 'CompanyController@updatePiece');
    //edit details piece
    Route::get('edit-details-piece/{piece_detail_id}', 'CompanyController@editDetailsPiece');
    //update details piece
    Route::post('update-details-piece/{pieceDetails_id}', 'CompanyController@updateDetailPiece');
    ///delete piece
    Route::get('delete-piece/{piece_id}', 'CompanyController@destroypiece');

    //delete pieceDetails
    Route::get('delete-details-piece/{piece_detail_id}', 'CompanyController@destroyDetailspiece');

    //add form
    Route::post('add-form', 'CompanyController@addForm');
    //display my forms
    Route::post('display-my-form', 'CompanyController@displayForms');
    //delete piece from collection
    Route::get('delete-piece-from-collection/{piece_detail_id}', 'CompanyController@deleteDetailPieceFromCollection');

    Route::get('delete-collection/{collection_id}', 'CompanyController@destroyCollection');
    //add comment to price
    Route::post('add-comment/{piece_id}', 'CompanyController@addComment');
    //delete form
    Route::get('delete-form/{form_id}', 'CompanyController@destroyForm');
    //add piece to collection cart
    Route::get('add-piece-to-collection-cart/{piece_id}/{type}', 'CompanyController@addDetailPieceToCollection');
    //delete piece from collection cart
    Route::get('delete-piece-from-collection-cart/{piece_id}', 'CompanyController@deleteDetailPieceFromCollectionCart');
    
    //display collection cart
    Route::post('display-collection-cart', 'CompanyController@displayCollectionCart');
    //confirm collection 
    Route::post('confirm-collection', 'CompanyController@confirmPieccesCollection');
    //display company collection
    Route::post('display-collection', 'CompanyController@displayCollection');
    //destroy collection
    Route::get('destroy-collection/{collection_id}', 'CompanyController@destroyCollection');

    Route::get('display-collection-details/{id}', 'CompanyController@displayDetailedCollection');

    Route::get('display-pieces-and-details', 'CompanyController@desplayAllPiecesAndPiecesDetail');
    
    ###################### Begin Statistics ##########################
    //count following
    Route::post('count-following', 'CompanyController@CountFollowers');
    //count my pieces
    Route::post('count-pieces', 'CompanyController@CountPieces');
    ###################### End Statistics ##########################

    ###################### Begin Notifications ##########################
    Route::post('show-notifications', 'CompanyController@displayNotifications');
    Route::get('read-notification/{id}', 'CompanyController@readNotification');
    ###################### End Notifications ##########################

    ###################### Begin Predictions #############################
    Route::post('show-predictions', 'CompanyController@getPredictedPieces');
    ###################### End Predictions #############################

    ###################### Begin company data ##########################
    Route::get('get-company-data', 'CompanyController@getCompanyData');
    
    ###################### End company data ###########################

    
    

    
    
    
    
    

    
});

Route::group(['middleware'=>'api', 'prefix'=>'company'], function(){
    Route::post('login', 'CompanyController@login') ;
    Route::post('register', 'CompanyController@register') ;
    Route::get('test', 'CompanyController@test');
Route::post('get-usage', 'CompanyController@getUsage');
    Route::post('get-season', 'CompanyController@getSeasons');
    Route::post('get-sub-category', 'CompanyController@getSubCategories');
    Route::post('get-color', 'CompanyController@getColor');
    Route::post('get-size', 'CompanyController@getSize');
    Route::post('get-master-category', 'CompanyController@getMasterCategories');

   
});


