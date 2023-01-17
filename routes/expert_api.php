<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//////////////////// Constants /////////////////////////////
define('NUM_NEWS_EXPERT_PROFILE', 3);
define('NUM_PIECES_EXPERT_PROFILE', 3);
/////////////////////////////////////////////////////////

Route::group(['middleware'=>'check_guards:expert-api', 'prefix'=>'expert'], function(){
    Route::post('get-usage', 'ExpertController@index');  //just for example
    Route::post('logout', 'ExpertController@logout');
   //expert profile  
    Route::post('expert-profile', 'ExpertController@displayExpertProfile');
    //form edit form
    Route::post('edit-expert-profile', 'ExpertController@editExpertProfile');
    //save edit form
    Route::post('update-expert-profile', 'ExpertController@updateExpertProfile');
    //add fashion news
    Route::post('add-fashion-news', 'ExpertController@addFashionNews');
    //show all my news
    Route::post('show-my-fashion-news', 'ExpertController@displayMyNews');
    //delete my news
    Route::get('delete_my_news/{news_id}', 'ExpertController@deleteMyNews');
    //add pieces
    Route::post('add-piece', 'ExpertController@addPiece');
    //edit piece
    Route::get('edit-piece/{piece_id}', 'ExpertController@editmypiece');
    //update piece
    Route::post('update-piece/{piece_id}', 'ExpertController@updatePiece');
     //display all pieces
    Route::post('display-my-pieces', 'ExpertController@displayMyPieces');
     //add details to an existing piece
     Route::post('add-details-to- piece/{pieces_id}', 'ExpertController@addDetailsPieceExpert');
     //edit details piece
     Route::get('edit-details-piece/{piece_detail_id}', 'ExpertController@editDetailsPiece');
     //update details piece
     Route::post('update-details-piece/{pieceDetails_id}', 'ExpertController@updateDetailPiece');
     //display all the piecesDetails
    Route::get('display-detail-pieces/{id}', 'ExpertController@displayDetailedPiece');
    ///delete piece
    Route::get('delete-piece/{piece_id}', 'ExpertController@destroypiece');
    //delete pieceDetails
    Route::get('delete-details-piece/{piece_detail_id}', 'ExpertController@destroyDetailspiece');
    //add msg
    Route::post('send-msg-to-user/{user_id}', 'ExpertController@sendMsgToUser');
    //display chat with user
    Route::get('display-chat-with-user/{user_id}', 'ExpertController@displayChatWithUser');
    //add comment to price
    Route::post('add-comment/{piece_id}', 'ExpertController@addComment');
    //count my pieces
    Route::post('count-pieces', 'ExpertController@CountPieces');
    ###################### Begin Notifications ##########################
    Route::post('show-notifications', 'ExpertController@displayNotifications');
    Route::get('read-notification/{id}', 'ExpertController@readNotification');
    ###################### End Notifications ##########################

     ###################### Begin Predictions #############################
     Route::post('show-predictions', 'ExpertController@getPredictedPieces');
     ###################### End Predictions #############################

     ###################### Begin expert data ##########################
     Route::get('get-expert-data', 'ExpertController@getExpertData');
     
     ###################### End expert data ############################
    
    
});

Route::group(['middleware'=>'api', 'prefix'=>'expert'], function(){
    Route::post('login', 'ExpertController@login') ;
    Route::post('register', 'ExpertController@register') ;
});