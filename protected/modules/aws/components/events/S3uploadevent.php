<?php

    class S3uploadevent extends CEvent{
        public $url;
        public function __construct(S3helper $s3helper)
        {
            parent::__construct($s3helper);
        }
    }

?>