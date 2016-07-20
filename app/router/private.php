<?php

Router::get( enact_cpSlug('email-newsletter/'), function(){

    $data = Array();

    $SubsModel                  = new \EmailNewsletter\model\Email;
    $data['subscriberCount']    = $SubsModel->select('COUNT(*) AS count')->first()['count'];

    $CampaignModel              = new \EmailNewsletter\model\Campaign;
    $data['campaignCount']      = $CampaignModel->select('COUNT(*) AS count')->first()['count'];

    \View::title('Email Newsletter Index');

    \View::style('
            .en-link {
                background-color:#ECECEC;
                display:block;
                max-width:200px;
                margin:0 auto;
                padding:12px;
            }
            .en-link:hover {
                background-color:#222;
                color:#FFF;
            }
        ');

    \Template::with('@emailnewsletter/index.html',$data);

});


Router::get(enact_cpSlug('email-newsletter/subscriptions/'), '\EmailNewsletter\controller\Subscription@getIndex');

Router::allowUrlParameters(Array('search'))->get( enact_cpSlug('email-newsletter/subscriptions/data/page/{page}/{sort_on}/{sort_type}'), '\EmailNewsletter\controller\Subscription@getData')
    ->where(Array(
        'page' => 'integer',
        'sort_on'=>'[a-z_]+',
        'sort_type'=>'(asc|desc)'
    ));

Router::get( enact_cpSlug('email-newsletter/subscriptions/export'), '\EmailNewsletter\controller\Subscription@getExport');

Router::delete( enact_cpSlug('email-newsletter/subscriptions/{id}'), '\EmailNewsletter\controller\Subscription@deleteSubscription')
    ->where('id','integer');


Router::get(enact_cpSlug('email-newsletter/campaigns/'), '\EmailNewsletter\controller\Campaign@getIndex');

Router::allowUrlParameters(Array('search'))->get( enact_cpSlug('email-newsletter/campaigns/data/page/{page}/{sort_on}/{sort_type}'), '\EmailNewsletter\controller\Campaign@getData')
    ->where(Array(
        'page' => 'integer',
        'sort_on'=>'[a-z_]+',
        'sort_type'=>'(asc|desc)'
    ));

Router::get( enact_cpSlug('email-newsletter/campaigns/create'), '\EmailNewsletter\controller\Campaign@getCreateCampaign');
Router::post( enact_cpSlug('email-newsletter/campaigns/create'), '\EmailNewsletter\controller\Campaign@postCreateCampaign');


Router::get( enact_cpSlug('email-newsletter/campaigns/{id}'), '\EmailNewsletter\controller\Campaign@getCampaignStats')
    ->where('id','integer');

