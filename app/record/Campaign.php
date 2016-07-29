<?php
namespace EmailNewsletter\record;

class Campaign extends \Disco\classes\Record {

    protected $model = '\EmailNewsletter\model\Campaign';
    
    

    /**
     * @var null|string $autoIncrementField The autoincrement field name.
    */
    protected $fieldDefinitions = array (
  'id' => 
  array (
    'null' => false,
    'type' => 'int',
    'length' => '11',
  ),
  'subject' => 
  array (
    'null' => false,
    'type' => 'varchar',
    'length' => '420',
  ),
  'template' => 
  array (
    'null' => false,
    'type' => 'varchar',
    'length' => '180',
  ),
  'token' => 
  array (
    'null' => false,
    'type' => 'varchar',
    'length' => '256',
  ),
  'total_sent' => 
  array (
    'null' => false,
    'type' => 'int',
    'length' => '11',
  ),
  'total_viewed' => 
  array (
    'null' => false,
    'type' => 'int',
    'length' => '11',
  ),
  'total_click_through' => 
  array (
    'null' => false,
    'type' => 'int',
    'length' => '11',
  ),
  'created_on' => 
  array (
    'null' => false,
    'type' => 'datetime',
  ),
  'variables' => 
  array (
    'null' => true,
    'type' => 'text',
  ),
);

    /**
     * @var null|string $autoIncrementField The autoincrement field name.
    */
    protected $autoIncrementField = 'id';




}//EnactEmailNewsletterCampaign
