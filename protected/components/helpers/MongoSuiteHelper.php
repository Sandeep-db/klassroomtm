<?php
class MongoSuiteHelper 
{
    /**
     * This function provides a middleware for updateAll function for YiiMongoSuite
     * @link http://canni.github.io/YiiMongoDbSuite/xhtml/advanced.partial-batch-update.html Documentation for YiiMongoSuite modifier
     * @param array $fields 
     * @return object object of type EMongoModifier  
     */
    public static function addMongoModifiers($fields = null) {
        $modifiers = new EMongoModifier();
        if(empty($fields)) {
            return $modifiers;
        }
        foreach($fields as $field_name => $field_value) {
            $operation_type = empty($field_value[1]) ? 'set' : $field_value[1];
            $value = is_array($field_value) ? $field_value[0] : $field_value ;
            $modifiers->addModifier($field_name, $operation_type , $value);
        }
        return $modifiers;
    }
}