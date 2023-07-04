<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->render('upload');
	}
	public function actionHii(){
		echo "Hii";
	}
}