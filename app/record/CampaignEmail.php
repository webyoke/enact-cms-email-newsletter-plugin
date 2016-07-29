<?php
namespace EmailNewsletter\record;

class CampaignEmail extends \Disco\classes\Record {

    protected $model = '\EmailNewsletter\model\CampaignEmail';
    
    

    /**
     * @var null|string $autoIncrementField The autoincrement field name.
    */
    protected $fieldDefinitions = array (
  'campaign_id' => 
  array (
    'null' => false,
    'type' => 'int',
    'length' => '11',
  ),
  'email_id' => 
  array (
    'null' => false,
    'type' => 'int',
    'length' => '11',
  ),
  'viewed_on' => 
  array (
    'null' => true,
    'type' => 'datetime',
  ),
  'clicked_through_on' => 
  array (
    'null' => true,
    'type' => 'datetime',
  ),
);

    /**
     * @var null|string $autoIncrementField The autoincrement field name.
    */
    protected $autoIncrementField = false;




}//CampaignEmail
