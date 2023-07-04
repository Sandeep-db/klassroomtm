<?php
    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;
    class DemoController extends Controller
    {

        // public $s3demo;

        // public function __construct()
        // {   
        //     $this->s3demo = new S3helper();
        // }
        
        public function saveindb($name,$path){
            $model = new S3demo();
            $model->name = $name;
            $model->url = $path;
            $model->save();
        }

        public function actionUpload(){
            if(isset($_POST['name'])){
                // print_r($_POST);
                // print_r($_POST['name']);
                $s3demo = new S3helper();
                $s3demo->putObject($_POST['name'],'/data/live/images/'.$_POST['path']); 
                $s3demo->attachEventHandler('onTest', function($event) {
                    echo "Attached Event2 raised<br />";
                    echo "Hello";
                    // var_dump($event);
                }); 
                $this->saveindb($_POST['name'],$_POST['path']);
            }
            $this->render('upload');
        }

        public function actionGetobject(){
            $s3demo = new S3helper();
            $s3demo->getObject();
        }

        public function actionGetobjecturl(){
            if(isset($_POST['name'])){
                $s3demo = new S3helper();
                $s3demo->getObjectUrl($_POST['name']);
            }
            $this->render('get');
        }

        
        
    }

?>