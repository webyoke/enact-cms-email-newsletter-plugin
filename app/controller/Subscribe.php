<?php
namespace EmailNewsletter\controller;

/**
 * Controller class for handling email subscriptions and unsubscriptions.
*/
class Subscribe extends \Disco\classes\Controller {



    /**
     * POST `/email-newsletter-subscribe`.
     *
     *
     * Add a new email to the subscribers list. Emails are added via the pre-provided form loaded by 
     * `@emailnewsletter:subscribe-form.html` or by creating your own form and pointing it to 
     * `email-newsletter-subscribe`.
     *
     * In the POST data you must provide:
     * - `email` : The email to add to the subscribers list.
     * - `submit-uri` : The URI to redirect to after the action, whether it fails or not.
     *
     * Determine success or error by checking the session flash data:
     * - `email-newsletter-subscribe-error` : Set to a string providing information about the error.
     * - `email-newsletter-subscribe-success` : Set to `Thanks for subscribing!` if email was successfully subscribed.
     *
     * You can do AJAX by defining the POST variable `ajax`, if defined the reponse will contain either:
     * - `success` - Set if successful with the message `Thanks for subscribing!`.
     * - `error` - Set if the email could not be subscribed, with a message describing the reason.
    */
    public function postSubscribe(){

        $data = Array();

        $ajax = \Data::post('ajax');

        $data['email'] = trim(\Data::post('email'));

        if(stripos($data['email'],'@') !== false){
            
            $data['ip_address'] = \Request::ip();
            $data['created_on'] = Array('raw' => 'NOW()');
            $data['token']      = \Crypt::hash(time() . $data['email']);

            $record = new \EmailNewsletter\record\Email($data);

            try {

                $record->insert();

                $message = 'Thanks for subscribing!';

                if($ajax){
                    $this->json(Array('success' => $message));
                }//if

                \Session::setFlash('email-newsletter-subscribe-success',$message);

            } catch(\Exception $e){

                \Enact::logError($e->getMessage());

                $message = 'We were unable to subscribe you to our email list, please try again later.';

                if($ajax){
                    $this->json(Array('error' => $message));
                }//if

                \Session::setFlash('email-newsletter-subscribe-error',$message);

            }//catch

        } else {

            $message = 'Please enter a valid email address and try again';

            if($ajax){
                $this->json(Array('error' => $message));
            }//if

            \Session::setFlash('email-newsletter-subscribe-error',$message);

        }//el

        $this->redirect(\Data::post('submit-uri'));

    }//postSubscribe



    /**
     * GET request which unsubscribes a subscribed email. 
     *
     * You can overide the default unsubscribe template by providing your own unsubcribed message template by defining the template `unsubscribed` in your 
     * configured `emailTemplatesDir` directory. Default is `_emailnewsletter`.
    */
    public function getUnSubscribe($token){

        $record = \EmailNewsletter\record\Email::find(Array('token' => $token),'id');

        if($record){
            $record->delete();
        }//if

        $template = trim(enact()->config('EmailNewsletter')['emailTemplatesDir']) . '/unsubscribed';

        if(\Template::isTemplate($template)){

            \View::title('You Unsubscribed From Our Newsletter');
            \Template::with($template);

        } else {

            \Template::with('@emailnewsletter/unsubscribed.html');

        }//el

    }//getUnSubscribe



}//Subscribe
