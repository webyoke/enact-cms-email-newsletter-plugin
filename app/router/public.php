<?php

Router::post('/email-newsletter-subscribe','\EmailNewsletter\controller\Subscribe@postSubscribe');

Router::get('/email-newsletter-unsubscribe/{token}','\EmailNewsletter\controller\Subscribe@getUnSubscribe')
    ->where('token','alpha_numeric');

Router::allowURLParameters('link')->get('/email-newsletter/link/{campaign_token}/{user_token}','EmailNewsletter\controller\StatsGate@getLinkFollow')
    ->where(Array(
        'campaign_token'    => 'alpha_numeric',
        'user_token'        => 'alpha_numeric',
    ));

Router::get('/email-newsletter/viewed/{campaign_token}/{user_token}','EmailNewsletter\controller\StatsGate@getViewedOn')
    ->where(Array(
        'campaign_token'    => 'alpha_numeric',
        'user_token'        => 'alpha_numeric'
    ));
