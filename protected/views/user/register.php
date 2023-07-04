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
            height: 90vh;
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
            height: 600px;
        }
    </style>
</head>

<body>
    <?php if (Yii::app()->user->hasFlash('success')) : ?>
        <div class="mx-2 alert alert-success">
            <?php echo Yii::app()->user->getFlash('success'); ?>
        </div>
        <script>
            setTimeout(() => {
                $('.alert-success').fadeOut('slow')
                location.href = 'login'
            }, 1000)
        </script>
    <?php endif; ?>

    <?php if (Yii::app()->user->hasFlash('error')) : ?>
        <div class="mx-2 alert alert-danger">
            <?php echo Yii::app()->user->getFlash('error'); ?>
        </div>
        <script>
            setTimeout(() => {
                $('.alert-danger').fadeOut('slow')
            }, 3000)
        </script>
    <?php endif; ?>

    <div class="v-center">
        <div class="container d-flex justify-content-center">
            <div class="col-10">
                <h1 class="m-5 mb-3 text-center display-3">Classroom TM</h1>
                <div class="form d-flex p-0">
                    <div class="col-6 d-flex flex-column justify-content-center">
                        <div class="container d-flex m-3">
                            <h3 class="col-8 display-6">Sign up</h3>
                            <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-facebook"></span></a>
                            <a href="#" class="social-icon d-flex align-items-center justify-content-center"><span class="fa fa-facebook"></span></a>
                        </div>
                        <div class="container m-3">
                            <?php echo CHtml::beginForm('register'); ?>
                            <label for="name" class="mx-2">Name</label>
                            <?php echo CHtml::textField('name', '', [
                                'required' => true,
                                'id' => 'name',
                                'class' => 'form-control m-2 rounded-pill',
                                'placeholder' => 'name',
                                'style' => 'width: 90% !important; height: 50px !important;'
                            ]); ?>
                            <label for="email" class="mx-2">Email</label>
                            <?php echo CHtml::emailField('email', '', [
                                'required' => true,
                                'id' => 'email',
                                'class' => 'form-control m-2 rounded-pill',
                                'placeholder' => 'email',
                                'style' => 'width: 90% !important; height: 50px !important;'
                            ]); ?>
                            <label for="passwd" class="mx-2">Password</label>
                            <?php echo CHtml::passwordField('passwd', '', [
                                'required' => true,
                                'id' => 'passwd',
                                'class' => 'form-control m-2 rounded-pill',
                                'placeholder' => 'password',
                                'style' => 'width: 90% !important; height: 50px !important;'
                            ]); ?>
                            <label for="cpasswd" class="mx-2">Confirm Password</label>
                            <?php echo CHtml::passwordField('cpasswd', '', [
                                'required' => true,
                                'id' => 'cpasswd',
                                'class' => 'form-control m-2 rounded-pill',
                                'placeholder' => 'confirm password',
                                'style' => 'width: 90% !important; height: 50px !important;'
                            ]); ?>
                            <div class="d-flex justify-content-center">
                                <?php echo CHtml::Button(
                                    'Register',
                                    [
                                        'type' => 'submit',
                                        'class' => 'btn btn-success m-2 text-center col-3 rounded-pill',
                                        'style' => 'height: 50px !important;'
                                    ]
                                ); ?>
                            </div>
                            <?php echo CHtml::endForm(); ?>
                        </div>
                    </div>
                    <div class="col-6 bg-success d-flex flex-column justify-content-center">
                        <h3 class="text-center text-white display-6">Welcome to Register</h3>
                        <div class="text-center">
                            <a href="login" class="text-white">Have an account? <br />click here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>