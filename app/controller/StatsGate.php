<?php
namespace EmailNewsletter\controller;

class StatsGate extends \Disco\classes\Controller {



    public function getViewedOn($campaign_token, $email_token){

        if($campaign_token !== 'test' && $email_token !== 'test'){

            $records = $this->getRecords($campaign_token, $email_token);

            $record = \EmailNewsletter\record\CampaignEmail::find(
                    Array(
                        'campaign_id'   => $records['campaign']['id'],
                        'email_id'      => $records['email']['id']
                    )
                );

            if($record && $record['viewed_on'] === null){

                $record['viewed_on'] = Array('raw' => 'NOW()');

                $records['campaign']['total_viewed'] = Array('raw' => 'total_viewed + 1');

                try {
                    $record->update();
                    $records['campaign']->update();
                } catch(\Exception $e){
                    \Enact::logError($e->getMessage());
                }//catch

            }//if

        }//if

        $this->file(EMAILNEWSLETTER_PLUGIN_PATH . '/resource/img/trace.png');

    }//getViewedOn



    public function getLinkFollow($campaign_token, $email_token){

        if($campaign_token !== 'test' && $email_token !== 'test'){

            $records = $this->getRecords($campaign_token, $email_token);

            $record = \EmailNewsletter\record\CampaignEmail::find(
                    Array(
                        'campaign_id'   => $records['campaign']['id'],
                        'email_id'      => $records['email']['id']
                    )
                );

            if($record && $record['clicked_through_on'] === null){

                $record['clicked_through_on'] = Array('raw' => 'NOW()');

                $records['campaign']['total_click_through'] = Array('raw' => 'total_click_through + 1');

                //didn't allow remote content so couldn't determine a view
                if($record['viewed_on'] === null){
                    $record['viewed_on'] = Array('raw' => 'NOW()');
                    $records['campaign']['total_viewed'] = Array('raw' => 'total_viewed + 1');
                }//if

                try {
                    $record->update();
                    $records['campaign']->update();
                } catch(\Exception $e){
                    \Enact::logError($e->getMessage());
                }//catch

            }//if

        }//if

        $link = \Data::get('link');

        if(!$link){
            $link = '/';
        } else {
            $link = urldecode($link);
        }//el

        $this->redirect($link);

    }//getLinkFollow



    private function getRecords($campaign_token, $email_token){

        $campaign = \EmailNewsletter\record\Campaign::Find(Array('token' => $campaign_token), 'id');

        $email = \EmailNewsletter\record\Email::Find(Array('token' => $email_token), 'id');

        return Array(
            'campaign'   => $campaign,
            'email'      => $email
        );

    }//resolveToIds



}//StatsGate
