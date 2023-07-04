<?php

    class S3demo extends EMongoDocument{
        public $name;
        public $url;
        public static function model($className = __class__){
            return parent::model($className);
        }

        public function getCollectionName(){
            return 's3demo';
        }
    }

?>