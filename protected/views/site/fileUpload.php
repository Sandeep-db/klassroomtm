<div class="form">
    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id' => 'upload-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?php echo $form->textField($model, 'name', ['class' => 'form-control py-2', 'required' => true]); ?>
    <?php echo $form->fileField($model, 'file', ['class' => 'form-control py-2', 'required' => true]); ?>

    <?php echo CHtml::submitButton('Upload', ['class' => 'btn btn-primary px-5 py-2']); ?>

    <?php $this->endWidget(); ?>
</div>