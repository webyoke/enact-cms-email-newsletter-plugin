<?php
namespace EmailNewsletter\controller;

class Campaign {



    /**
     * GET `/admin/email-newsletter/campaigns/`.
     *
     *
     * Campaigns management dashboard.
    */
    public function getIndex(){

        \Template::with('@emailnewsletter/campaign-index.html');

    }//getIndex



    /**
     * AJAX/JSON.
     * GET `/admin/email-newsletter/campaigns/data/page/{page}/{sort_on}/{sort_type}`.
     *
     * Return all campaigns as JSON.
     *
     *
     * @param int $page The page number.
     * @param string $sort_on What column to sort on.
     * @param string $sort_type A value of either "asc|desc" denoting how to order the results in conjunction with 
     * $sort_on.
     *
     * @return void 
    */
    public function getData($page,$sort_on,$sort_type) {

        \View::json();

        $data = Array();

        $page = enact_getPagination($page);

        $Model = new \EmailNewsletter\model\Campaign;

        $Model->alias('c')
            ->select('
                c.id,
                c.subject,
                c.template,
                c.total_sent,
                c.total_viewed,
                c.total_click_through,
                UNIX_TIMESTAMP(c.created_on) AS created_on
            ')
            ->order('c.' . $sort_on . ' ' . $sort_type)
            ->limit($page['offset'],$page['per_page']);


        $search = \Data::get('search');
        if($search !== false){
            $search = '%' . $search . '%';
            $Model->whereLike(Array('c.subject' => $search));
        }//if

        $data['data'] = $Model->asArray();

        enact_ajaxResponse($data);

    }//getData



    public function getCreateCampaign(){

        \View::title('Create Email Campaign');

        enact()->with('Plugin')->getPlugin('EmailNewsletter')->includeResource('/js/jsx/CreateCampaign.js');

        \View::html('<div id="create-campaign"></div>');

        $templates = Array();

        $template_paths = glob(ENACT_PATH . 'template/' . $this->emailTemplatePath() . '*.html');

        foreach($template_paths as $path){

            $data = Array();

            $data['template'] = basename($path);

            $variable_path = str_replace('.html','.php',$path);

            if(is_file($variable_path)){
                $data['variables'] = require $variable_path; 
            }//if

            $templates[] = $data;

        }//foreach

        $templates = json_encode($templates);

        \View::script('ReactDOM.render(React.createElement(Enact.react_class.CreateCampaign, { templates : ' . $templates . ' } ),document.getElementById("create-campaign"));');

    }//getCreateCampaign



    /**
     * JSON/AJAX
     * POST `/admin/email-newsletter/campaign/create`.
     *
     * Create or test a campaign.
    */
    public function postCreateCampaign(){

        \View::json();

        $test = \Data::post('test');
        $data = \Data::post('data');

        foreach($data['variables'] as $k => $v){
            if(is_string($v)){
                $data['variables'][$k] = str_replace("\n",'<br>',$v);
            }//if
        }//foreach

        if($test){

            $this->registerTwigFunctions();

            $email = (new \Enact\template\lookup\User)->id(enact()->with('User')->userId())->first()->email;

            $data['variables']['campaignToken'] = 'test';
            $data['variables']['userToken'] = 'test';

            $this->sendCampaignEmail($email,$data['subject'],$data['template'],$data['variables']);

            enact_ajaxResponse("Success! `{$email}` will receieve an email momentarily with the test of the campaign.");

        }//if


        $data['variables'] = json_encode($data['variables']);

        $data['created_on'] = Array('raw' => 'NOW()');

        $data['token'] = \Crypt::hash(time() . $data['subject']);

        $data['total_sent'] = (new \EmailNewsletter\model\Email)->select('COUNT(*) AS count')->first()['count'];
        $data['total_viewed'] = 0;
        $data['total_click_through'] = 0;

        $record = new \EmailNewsletter\record\Campaign($data);

        try {

            $record->insert();

            \Queue::push(function($id){

                $plugin = enact()->with('Plugin')->getPlugin('EmailNewsletter');

                $plugin->onBoot();

                $controller = new \EmailNewsletter\controller\Campaign;

                $controller->sendCampaign($id);

            },1,Array($record['id']));

        } catch(\Exception $e){
            \Enact::logError($e->getMessage());
            enact_ajaxResponse(false,true);
        }//catch


        enact_ajaxResponse('The campaign has been started!');

    }//postCreateCampaign



    public function sendCampaign($id){

        $campaign = new \EmailNewsletter\record\Campaign(Array('id' => $id));

        $campaign->fetch(Array('subject','template','variables','token'));

        $variables = json_decode($campaign['variables'],true);

        $variables['campaignToken'] = $campaign['token'];

        $this->registerTwigFunctions();

        $subscriptions = (new \EmailNewsletter\model\Email)
            ->select('
                id,
                email,
                token
            ')
            ->data();

        while($sub = $subscriptions->fetch()){

            $variables['userToken'] = $sub['token'];

            $campaign_email = new \EmailNewsletter\record\CampaignEmail;
            $campaign_email['campaign_id'] = $campaign['id'];
            $campaign_email['email_id'] = $sub['id'];

            $campaign_email->insert();

            $this->sendCampaignEmail($sub['email'],$campaign['subject'],$campaign['template'],$variables);

        }//while

    }//sendCampaign



    public function getCampaignStats($id){

        $campaign = \EmailNewsletter\record\Campaign::find(Array('id' => $id));

        if($campaign === false){
            return false;
        }//if

        $campaign = $campaign->getFields();

        $campaign['created_on_unix'] = (new \DateTime($campaign['created_on']))->getTimestamp();

        $stats = (new \EmailNewsletter\model\CampaignEmail)
            ->select('
                UNIX_TIMESTAMP(viewed_on) AS viewed_on,
                UNIX_TIMESTAMP(clicked_through_on) AS clicked_through_on
            ')
            ->where('campaign_id=?',$campaign['id'])
            ->order('viewed_on')
            ->asArray();


        \View::title("Email Campaign #{$campaign['id']} Stats");

        $campaign = json_encode($campaign);
        $stats = json_encode($stats);

        enact()->with('Plugin')->getPlugin('EmailNewsletter')->includeResource('/js/jsx/CampaignStats.js');

        \View::scriptSrc('//d3js.org/d3.v3.min.js');

        \View::html('<div id="campaign-stats"></div>');
        \View::script("
                ReactDOM.render(
                        React.createElement(Enact.react_class.CampaignStats, { campaign : {$campaign}, stats : {$stats} }), 
                        document.getElementById('campaign-stats')
                    );
            ");

        \View::style('
            path.viewed-on-line {
                stroke:#2EBBFF;
                fill:none;
            }
            path.clicked-on-line {
                stroke:#21FFCF;
                fill:none;
            }

            svg text {
                stroke:#222;
            }

            svg .axis .domain {
                fill: none; 
                shape-rendering:crispedges;
                stroke:#222;
            }
            svg .axis .tick line {
                stroke:#222;
            }
        ');

    }//getCampaignStats



    private function sendCampaignEmail($to_email,$subject,$template,$variables){

        $variables['email'] = $to_email;
        $variables['subject'] = $subject;

        $body = \Template::build($this->emailTemplatePath() . $template, $variables);

        try {

            enact()->with('Email')->send($to_email,$subject,$body);

        } catch(\Exception $e){

            \Enact::logError($e->getMessage());

        }//catch

    }//sendCampaign



    private function registerTwigFunctions(){

        \Template::addFunction(new \Twig_SimpleFunction('emailLink', function($context,$link){
            return enact()->domain . "/email-newsletter/link/{$context['campaignToken']}/{$context['userToken']}?link=" . urlencode($link);
        },Array('needs_context' => true)));

        \Template::addFunction(new \Twig_SimpleFunction('emailUnSubLink', function($context){
            return enact()->domain . "/email-newsletter-unsubscribe/{$context['userToken']}";
        },Array('needs_context' => true)));

        \Template::addFunction(new \Twig_SimpleFunction('emailViewedOnImg', function($context){
            return \Html::img(Array('src' => enact()->domain . "/email-newsletter/viewed/{$context['campaignToken']}/{$context['userToken']}"));
        },Array('needs_context' => true)));

    }//registerTwigFunctions



    private function emailTemplatePath(){
        return trim(enact()->config('EmailNewsletter')['emailTemplatesDir'],'/') . '/';
    }//emailTemplatePath


}//Campaign
