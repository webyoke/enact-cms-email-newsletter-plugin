<?php
namespace EmailNewsletter\record;

class CampaignEmail extends \Disco\classes\Record {

    protected $model = '\EmailNewsletter\model\CampaignEmail';
    
    
    /**
    * @var Array $validators The validation rules for the fields.
    */
    public static $validators = null;


    public static function autoIncrementField(){
        return false;
    }//autoIncrementField


    /**
     * Get the fields of the record.
     *
     *
     * @return array The fields.
     */
    public static function fieldNames(){
        return Array(
    
                'campaign_id',
                'email_id',
                'viewed_on',
                'clicked_through_on'
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
    
                    'campaign_id',
                    'email_id'
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
    
                'campaign_id' => \Respect\Validation\Validator::intType()->between(0,99999999999),
            
                'email_id' => \Respect\Validation\Validator::intType()->between(0,99999999999),
            
                'viewed_on' => \Respect\Validation\Validator::date(),
            
                'clicked_through_on' => \Respect\Validation\Validator::date(),
            
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

                case 'campaign_id' : 
                    return self::$validators['campaign_id']->validate($v) || parent::rawType($v);
            
                case 'email_id' : 
                    return self::$validators['email_id']->validate($v) || parent::rawType($v);
            
                case 'viewed_on' : 
                    return self::$validators['viewed_on']->validate($v) || parent::nullType($v) || parent::rawType($v);
            
                case 'clicked_through_on' : 
                    return self::$validators['clicked_through_on']->validate($v) || parent::nullType($v) || parent::rawType($v);
            
                default:
                    throw new \Disco\exceptions\Record("Record validation exception, record does not have a field `{$field}` to validate against");
        }//switch
    }//validate


}//CampaignEmail
