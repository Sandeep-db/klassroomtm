<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darwinbox</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>
        .v-center {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        a {
            text-decoration: none;
        }

        .form {
            box-shadow: 0px 2px 15px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 16px;
            height: 450px;
        }
    </style>
</head>

<body>
    <div class="v-center">
        <div class="container d-flex justify-content-center">
            <div class="col-10">
                <!-- <form action="loginuser" method="POST"> -->
                <h1 class="m-5 text-center display-3">Classroom TM</h1>
                <div class="form d-flex p-0">
                    <div class="col-6 d-flex flex-column justify-content-center">
                        <div class="container d-flex m-3">
                            <h3 class="col-8 display-6">Sign in</h3>
                            <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-facebook"></span></a>
                            <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-facebook"></span></a>
                        </div>
                        <div class="container m-3">
                            <?php $form = $this->beginWidget('CActiveForm', array(
                                'id' => 'login-form',
                                'enableClientValidation' => true,
                                'clientOptions' => array(
                                    'validateOnSubmit' => true,
                                ),
                            )); ?>
                            <label for="email" class="mx-3">Email</label>
                            <?php echo $form->emailField($model, 'email', [
                                'required' => true,
                                'id' => 'email',
                                'class' => 'form-control m-2 rounded-pill',
                                'placeholder' => 'email',
                                'style' => 'width: 90% !important; height: 50px !important;',
                            ]); ?>

                            <label for="passwd" class="mx-3">Password</label>
                            <?php echo $form->passwordField($model, 'passwd', [
                                'required' => true,
                                'id' => 'passwd',
                                'class' => 'form-control m-2 rounded-pill',
                                'placeholder' => 'password',
                                'style' => 'width: 90% !important; height: 50px !important;'
                            ]); ?>
                            <?php echo $form->error($model, 'email', [
                                'class' => 'mx-2 alert alert-danger',
                                'style' => 'width: 90% !important;'
                            ]) ?>
                            <script>
                                setTimeout(() => {
                                    $('.alert-danger').fadeOut('slow')
                                }, 5000)
                            </script>
                            <div class="d-flex justify-content-center">
                                <?php echo CHtml::Button(
                                    'Login',
                                    [
                                        'type' => 'submit',
                                        'class' => 'btn btn-success m-2 text-center col-3 rounded-pill',
                                        'style' => 'height: 50px !important;'
                                        // 'onclick' => 'login()'
                                    ]
                                ); ?>
                            </div>
                            <?php $this->endWidget(); ?>
                        </div>
                    </div>
                    <div class="col-6 bg-success d-flex flex-column justify-content-center">
                        <h3 class="text-center text-white display-6">Welcome to Login</h3>
                        <div class="text-center">
                            <a href="register" class="text-white">Don't have account? <br />click here</a>
                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function login() {
            const form = document.forms['login-form']
            const formData = new FormData(form)
            var data = {
                'ajax': 'login-form'
            }
            for (const [k, v] of formData.entries()) {
                data[k] = v
            }
            console.log(data)
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::app()->createAbsoluteUrl("home/login"); ?>',
                data: data,
                dataType: 'html',
                success: function(result) {
                    result = JSON.parse(result)
                    console.log('result', result)
                    let nav = '<?php echo Yii::app()->createAbsoluteUrl("home/index"); ?>'
                    location.href = nav
                },
                error: function(err) {
                    console.log(err)
                },
            })
        }
    </script>

</body>