<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//////////////////// Constants /////////////////////////////
define('NUM_NEWS_USER_PROFILE', 6);
define('LIMIT_NUM_NEWS', 3);
define('LIMIT_NUM_PIECES', 3);
define('NUM_PIECES_IN_HOME', 6);
define('NUM_COMPANIES_IN_FILTER', 10);
define('NUM_EXPERTS_IN_FILTER', 6);


/////////////////////////////////////////////////////////

Route::group(['middleware'=>'check_guards:user-api', 'prefix'=>'user'], function(){
    ################### Begin user profile ##############################

    Route::post('user-profile', 'TableUserController@displayUserProfile');
    //form edit form
    Route::post('edit-user-profile', 'TableUserController@editUserProfile');
    //save edit form
    Route::post('update-user-profile', 'TableUserController@updateUserProfile');

    ################### End user profile ################################
    ################### Begin Displays pieces & details & news & collections ##########

    //display all fashion news
    Route::get('display-all-news', 'HomeController@displayAllFashionNews');
    //display last fashion news
    Route::post('display-last-3-news', 'HomeController@displayLastFashionNews');
    Route::post('display-last-3-pieces', 'HomeController@displayLatestpieces');
    //display details of a piece
    Route::get('display-details-pieces/{piece_id}', 'HomeController@displaypiecesDetails');
    //display all pieces 
    Route::get('display-all-pieces', 'HomeController@displayAllPieces');
    // answer to form(rate form)
    Route::post('rate-form/{form_id}', 'TableUserController@ratingForm');
    //display companies collections
    Route::post('display-collections', 'HomeController@displayCompaniesCollection');
    //display detailed pieces of a collection
    Route::get('display-details-collection/{id}', 'HomeController@displayDetailsCollection');

    ################### End Displays pieces & details & news ###########
    ################### Begin Favorites & likes ###############################

    //add pieces to favorite
    Route::get('add-piece-to-favorite/{pieceId}', 'TableUserController@addPieceToFavorite');
    //display my  favorite pieces 
    Route::post('display-my-favorite-pieces', 'TableUserController@displayMyFavorite');
    //follow company
    Route::get('follow-to-company/{companyId}', 'TableUserController@FollowToCompany');
    //display pieces for company followed
    Route::post('display-pieces-for-company-followed', 'TableUserController@displayPiecesForCompanyFollowed');

    Route::get('get-followed-companies', 'TableUserController@getFollowedCompanies');
    
    ################### End Favorites & likes ###############################
    ################### Begin messages ###############################

    //add msg
    Route::post('add-msg/{expert_id}', 'TableUserController@sendMsgToExpert');
    //display chat with expert
    Route::get('display-chat-with-expert/{expert_id}', 'TableUserController@displayChatWithExpert');   
    //experts who my communite
    Route::post('experts-who-my-communite', 'TableUserController@displayChatsUser');
    //add comment to price
    Route::post('add-comment/{piece_id}', 'TableUserController@addComment');

    ################### End messages ###############################
    ################ Begin Recommendations ######################
    //recommendations on pieces
    Route::get('recommendations1/{pieceId}', 'HomeController@recommendations1');
    Route::get('recommendations2/{pieceId}', 'HomeController@recommendations2');
    Route::get('recommendations3/{pieceId}', 'HomeController@recommendations3');
    //you may like
    Route::get('you-may-like/{pieceId}', 'HomeController@youMayLike');
    //sort by 
    Route::post('sort-by', 'HomeController@sortBy');
    ############## End Recommendations #####################
    ############## Begin searching & filtering #########################

    //auto-complete (ajax code in gt-suggestion.blade.php)
    Route::post('auto-complete', 'HomeController@autocompleteSearch')->name('autocomplete-search');
    //find the search
    Route::post('search-in-box', 'HomeController@searchInBox');
    //filtering
    Route::post('filter/{sub_category}/{master_category}/{type}/{type_id}', 'HomeController@filterRequests');
    ///////// routes for pre-filtering ////////////////////
    //get all sub categories belong to master category


    Route::get('sub-category/{masterCategory}', 'HomeController@getSubCategoriesBelongToMaster');
    //get all pieces belong to apparel with specific sub_category
    Route::get('piece-in-apparel/{subCategory}', 'HomeController@getApparelPieces');
    //get all pieces belong to Accessories with specific sub_category
    Route::get('piece-in-accessories/{subCategory}', 'HomeController@getAccessoriesPieces');
    //get all pieces belong to Footwear with specific sub_category
    Route::get('piece-in-footwear/{subCategory}', 'HomeController@getFootwearPieces');


    //display companies in filter page
    Route::post('get-all-companies', 'HomeController@getCompanies');
    //display experts in filter page
    Route::post('get-all-experts', 'HomeController@getExperts');
    //get all pieces for company
    Route::get('pieces-for-company/{company_id}', 'HomeController@getPiecesForCompany');

//get all pieces for expert
    Route::get('pieces-for-expert/{expert_id}', 'HomeController@getPiecesForExpert');
    
    ############# End searching & filtering ############################
    ###################### Begin Notifications ##########################
    Route::post('show-notifications', 'HomeController@displayNotifications');
    Route::get('read-notification/{id}', 'HomeController@readNotification');
    ###################### End Notifications ##########################

    ###################### Begin Predictions #############################
    Route::post('show-predictions', 'HomeController@getPredictedPieces');
    ###################### End Predictions #############################
    ###################### Begin user data #############################
    
    Route::get('get-user-data', 'TableUserController@getUserData');
    ##################### End user data ###############################
    #################### begin visiting other profiles ################
    Route::post('show-other-profile/{companyId}/{expertId}', 'HomeController@visitCompanyOrExpert');
    
    #################### end visiting other profiles ################

    //logout    
    Route::post('logout', 'TableUserController@logout');
    
});

Route::group(['middleware'=>'api', 'prefix'=>'user'], function(){
    Route::post('login', 'TableUserController@login') ;
    Route::post('register', 'TableUserController@register') ;
    Route::post('get-season', 'CompanyController@getSeasons');
    Route::post('get-sub-category', 'CompanyController@getSubCategories');
    Route::post('get-color', 'CompanyController@getColor');
    Route::post('get-size', 'CompanyController@getSize');
    Route::post('get-master-category', 'CompanyController@getMasterCategories');
});