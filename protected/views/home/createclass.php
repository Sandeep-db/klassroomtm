<div class="p-4 d-flex flex-column justify-content-center">
    <div class="container">
        <div class="row justify-content-end mt-3">
            <div class="card shadow p-5 pt-4">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'addcourse-form',
                    'enableAjaxValidation' => false,
                    'htmlOptions' => array(
                        'enctype' => 'multipart/form-data'
                    ),
                )); ?>
                <div class="spContainer mx-auto ">
                    <p class="text-success border-bottom border-success display-6 mt-3 mb-4 pb-2" style="font-size: 32px;"><i class="bi bi-mortarboard"></i> Create Class</p>
                    <div class="row mt-2">
                        <div class="d-inline text-left col mb-3">
                            <?php echo $form->labelEx($model, 'course_name'); ?>
                            <?php echo $form->textField($model, 'course_name', array('class' => 'form-control inpSp', 'placeholder' => 'Course Name')); ?>
                            <?php echo $form->error($model, 'course_name'); ?>
                        </div>
                        <div class="d-inline text-left col mb-3">
                            <?php echo $form->labelEx($model, 'course_code'); ?>
                            <?php echo $form->textField($model, 'course_code', array('class' => 'form-control inpSp', 'placeholder' => 'Course Code')); ?>
                            <?php echo $form->error($model, 'course_code'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-inline text-left mb-3">
                            <?php echo $form->labelEx($model, 'course_description'); ?>
                            <?php echo $form->textArea($model, 'course_description', array('class' => 'form-control inpSp', 'style' => 'height: 100px;', 'placeholder' => 'Course Description')); ?>
                            <?php echo $form->error($model, 'course_description'); ?>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="d-inline text-left mb-3 col">
                            <?php echo $form->labelEx($model, 'course_instructor'); ?>
                            <?php echo $form->textField($model, 'course_instructor', [
                                'id' => 'course_instructor',
                                'class' => 'form-control inpSp',
                                'placeholder' => 'Course Instructor',
                                'onkeyup' => 'debouncedSearch()',
                                'list' => 'users-names',
                                'autocomplete' => 'off',
                            ]); ?>
                            <?php echo $form->error($model, 'course_instructor'); ?>
                            <datalist id="users-names">
                            </datalist>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="d-inline text-left mb-3 col">
                            <?php echo $form->labelEx($model, 'course_start_date'); ?>
                            <?php echo $form->dateField($model, 'course_start_date', array('class' => 'form-control inpSp', 'placeholder' => 'Start Date')); ?>
                            <?php echo $form->error($model, 'course_start_date'); ?>
                        </div>
                        <div class="d-inline text-left mb-3 col">
                            <?php echo $form->labelEx($model, 'course_end_date'); ?>
                            <?php echo $form->dateField($model, 'course_end_date', array('class' => 'form-control inpSp')); ?>
                            <?php echo $form->error($model, 'course_end_date'); ?>
                        </div>
                    </div>
                    <?php if (Yii::app()->user->hasFlash('success')) : ?>
                        <div class="alert alert-success">
                            <?php echo Yii::app()->user->getFlash('success'); ?>
                        </div>
                        <script>
                            setTimeout(() => {
                                $('.alert-success').fadeOut('slow')
                                location.href = '/index.php/home/index'
                            }, 1000)
                        </script>
                    <?php endif; ?>
                    <?php if (Yii::app()->user->hasFlash('error')) : ?>
                        <div class="alert alert-danger">
                            <?php echo Yii::app()->user->getFlash('error'); ?>
                        </div>
                        <script>
                            setTimeout(() => {
                                $('.alert-danger').fadeOut('slow')
                            }, 3000)
                        </script>
                    <?php endif; ?>
                    <div class="d-flex flex-row-reverse">
                        <div class="d-inline mt-3">
                            <?php echo CHtml::resetButton('Reset', array('class' => 'mx-1 btn btn-danger')); ?>
                            <?php echo CHtml::submitButton('Create', array('class' => 'mx-1 btn btn-success')); ?>
                        </div>
                    </div>
                </div>
                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    function searchTeacher() {
        name = $('#course_instructor').val()
        $('#users-names').empty();
        if (name == '') {
            return
        }
        let data = {
            'ajax': 'search-teacher',
            name: name,
        }
        console.log(data)
        // return
        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createAbsoluteUrl("home/searchteacher"); ?>',
            data: data,
            dataType: 'html',
            success: function(result) {
                result = JSON.parse(result)
                console.log('result', result)
                for (let user of result) {
                    $('#users-names').append(`<option value="${user.email}">${user.name}</option>`);
                }
            },
            error: function(err) {
                console.log(err)
            },
        })
    }

    function debounce(fn, delay) {
        this.time_id = null
        return function(...args) {
            if (this.time_id) {
                clearInterval(this.time_id)
            }
            this.time_id = setTimeout(() => {
                fn(...args)
                this.time_id = null
            }, delay)
        }
    }

    let debouncedSearch = debounce(searchTeacher, 500)
</script>