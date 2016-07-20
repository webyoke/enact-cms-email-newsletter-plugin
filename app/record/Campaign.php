<?php
namespace EmailNewsletter\record;

class Campaign extends \Disco\classes\Record {

    protected $model = '\EmailNewsletter\model\Campaign';
    
    
    /**
    * @var Array $validators The validation rules for the fields.
    */
    public static $validators = null;


    public static function autoIncrementField(){
        return 'id';
    }//autoIncrementField


    /**
     * Get the fields of the record.
     *
     *
     * @return array The fields.
     */
    public static function fieldNames(){
        return Array(
    
                'id',
                'subject',
                'template',
                'token',
                'total_sent',
                'total_viewed',
                'total_click_through',
                'created_on',
                'variables'
        );
    }//fields



    /**
     * Get the fields of the record. (Wrapper for `self::fields()`).
     *
     *
     * @return array The fields.
     */
    public function getFieldNames(){
        return self::fieldNames();
    }//getFields



    /**
     * Get the required fields of the record (cannot be NULL).
     *
     *
     * @return array The fields.
     */
    public static function requiredFieldNames(){
        return Array(
    
                    'subject',
                    'template',
                    'token',
                    'total_sent',
                    'total_viewed',
                    'total_click_through',
                    'created_on'
        );
    }//requiredFields



    /**
     * Get the required fields of the record. (Wrapper for `self::requiredFields()`).
     *
     *
     * @return array The fields.
     */
    public function getRequiredFieldNames(){
        return self::requiredFieldNames();
    }//getRequiredFields



    /**
    * Register the records validators into `self::$validators`.
    *
    *
    * @return void
    */
    public static function registerValidators(){
        if(!self::$validators){
            self::$validators = Array(
    
                'id' => \Respect\Validation\Validator::intType()->between(0,99999999999),
            
                'subject' => \Respect\Validation\Validator::stringType()->length(0,420),
            
                'template' => \Respect\Validation\Validator::stringType()->length(0,180),
            
                'token' => \Respect\Validation\Validator::stringType()->length(0,256),
            
                'total_sent' => \Respect\Validation\Validator::intType()->between(0,99999999999),
            
                'total_viewed' => \Respect\Validation\Validator::intType()->between(0,99999999999),
            
                'total_click_through' => \Respect\Validation\Validator::intType()->between(0,99999999999),
            
                'created_on' => \Respect\Validation\Validator::date(),
            
                'variables' => \Respect\Validation\Validator::alwaysValid(),
            
            );
        }//if
    }//__registerValidators



    /**
    * Determine if a value is valid for a records particular field.
    *
    *
    * @param string $field The field of the record.
    * @param string $v The value to test against the field.
    *
    * @return boolean Did it validate?
    */
    public static function validate($field, $v){
        self::registerValidators();

        if(is_numeric($v)){
            if(stripos($v,'.') !== false){
                $v = (float)$v;
            } else {
                $v = (int)$v;
            }//el
        }//if

        switch($field){

                case 'id' : 
                    return self::$validators['id']->validate($v) || parent::nullType($v) || parent::rawType($v);
            
                case 'subject' : 
                    return self::$validators['subject']->validate($v) || parent::rawType($v);
            
                case 'template' : 
                    return self::$validators['template']->validate($v) || parent::rawType($v);
            
                case 'token' : 
                    return self::$validators['token']->validate($v) || parent::rawType($v);
            
                case 'total_sent' : 
                    return self::$validators['total_sent']->validate($v) || parent::rawType($v);
            
                case 'total_viewed' : 
                    return self::$validators['total_viewed']->validate($v) || parent::rawType($v);
            
                case 'total_click_through' : 
                    return self::$validators['total_click_through']->validate($v) || parent::rawType($v);
            
                case 'created_on' : 
                    return self::$validators['created_on']->validate($v) || parent::rawType($v);
            
                case 'variables' : 
                    return self::$validators['variables']->validate($v) || parent::nullType($v) || parent::rawType($v);
            
                default:
                    throw new \Disco\exceptions\Record("Record validation exception, record does not have a field `{$field}` to validate against");
        }//switch
    }//validate


}//Campaign
