<?php
namespace EmailNewsletter\record;

class Email extends \Disco\classes\Record {

    protected $model = '\EmailNewsletter\model\Email';
    
    

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
  'email' => 
  array (
    'null' => false,
    'type' => 'varchar',
    'length' => '480',
  ),
  'ip_address' => 
  array (
    'null' => false,
    'type' => 'varchar',
    'length' => '45',
  ),
  'created_on' => 
  array (
    'null' => false,
    'type' => 'datetime',
  ),
  'token' => 
  array (
    'null' => false,
    'type' => 'varchar',
    'length' => '256',
  ),
);

    /**
     * @var null|string $autoIncrementField The autoincrement field name.
    */
    protected $autoIncrementField = 'id';




}//Email
