<?php if (Yii::app()->user->hasFlash('success')) : ?>
<div>
    <p class="alert alert-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </p>
    <script type="text/javascript"> 
        setTimeout(() => {
            $('.alert-success').fadeOut('slow')
        }, 3000)
    </script>
</div>
<?php endif; ?>

<div class="d-flex justify-content-center">
    <div class="form col-6">
        <?php echo CHtml::beginForm('message', 'post'); ?>
        <h1 class="text-center my-5">Message Form</h1>
        <?php echo CHtml::hiddenField('email', $_COOKIE['email']) ?>
        <?php echo CHtml::label('Enter Message', 'message', ['class' => 'm-2']); ?>
        <?php echo CHtml::textField(
            'message',
            '',
            [
                'required' => true,
                'class' => 'form-control m-2',
                'placeholder' => 'Enter message',
            ]
        ); ?>
        <div class="d-flex justify-content-center">
            <?php echo CHtml::submitButton('Send Message', [
                'class' => 'btn btn-primary',
            ]); ?>
        </div>
        <?php echo CHtml::endForm(); ?>
    </div>
</div>
