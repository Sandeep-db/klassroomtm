<!-- Modal -->
<div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo CHtml::beginForm('', 'post', ['enctype' => 'multipart/form-data']) ?>
            <div class="modal-body">
                <?php echo CHtml::hiddenField('email', $_COOKIE['email']) ?>
                <?php echo CHtml::hiddenField('image-change', 'some-value') ?>
                <?php echo CHtml::fileField('image', '', ['class' => 'form-control', 'required' => true]) ?>
                <label for="img-passwd" class="mt-3 form-label">Password :</label>
                <?php echo CHtml::passwordField('passwd', '', ['class' => 'form-control', 'id' => 'img-passwd', 'required' => true]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <?php echo CHtml::submitButton('Change', ['class' => 'btn btn-success']) ?>
            </div>
            <?php echo CHtml::endForm() ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <h5 class="m-3">Delete profile picture</h5>
            <?php echo CHtml::beginForm('', 'post', ['enctype' => 'multipart/form-data']) ?>
            <div class="modal-body">
                <?php echo CHtml::hiddenField('email', $_COOKIE['email']) ?>
                <?php echo CHtml::hiddenField('delete-image', 'some-value') ?>
                <label for="img-passwd" class="form-label">Password :</label>
                <?php echo CHtml::passwordField('passwd', '', ['class' => 'form-control', 'id' => 'img-passwd', 'required' => true]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <?php echo CHtml::submitButton('Delete', ['class' => 'btn btn-danger']) ?>
            </div>
            <?php echo CHtml::endForm() ?>
        </div>
    </div>
</div>

<script>
    function showModal() {
        $('#update-modal').modal('show')
    }

    function deleteModal() {
        $('#delete-modal').modal('show')
    }
</script>

<div class="container-fluid p-4">
    <div class="shadow d-lg-flex">
        <div class="col-3">
            <div class="col-12 rounded p-5 pb-4">
                <!-- <img src="https://github.com/mdo.png" class="col-12 rounded-circle shadow-lg" alt=""> -->
                <img src="<?= Yii::app()->user->getState('image') ?>" class="col-12 rounded-circle shadow-lg" alt="">
                <div class="d-flex justify-content-center mt-3">
                    <button class="btn btn-success mx-2" onclick="showModal()">Change</button>
                    <button class="btn btn-danger mx-2" onclick="deleteModal()">Delete</button>
                    <!-- <button class="btn btn-danger mx-2">Delete</button> -->
                </div>
            </div>
        </div>
        <div class="col-9 p-3 pl-5">
            <p class="display-6 mb-5">Your Details</p>
            <div class="form">
                <?php echo CHtml::beginForm('profile'); ?>
                <?php echo CHtml::hiddenField('profile-change', 'some-value') ?>
                <div class="d-flex mt-3">
                    <label for="name" class="col-2 h5 mt-2">Name: </label>
                    <div class="col-10">
                        <?php echo CHtml::textField('name', $_COOKIE['name'], [
                            'id' => 'name',
                            'class' => 'form-control',
                            'placeholder' => 'Enter Your Name',
                        ]); ?>
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <?php echo CHtml::hiddenField('email', $_COOKIE['email']) ?>
                    <label for="email_" class="col-2 h5 mt-2">Email: </label>
                    <div class="col-10">
                        <?php echo CHtml::emailField('email', $_COOKIE['email'], [
                            'disabled' => true,
                            'id' => 'email_',
                            'class' => 'form-control',
                            'placeholder' => 'Your Email',
                        ]); ?>
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <label for="passwd" class="col-2 h5 mt-2">Current Password: </label>
                    <div class="col-10">
                        <?php echo CHtml::passwordField('passwd', '', [
                            'required' => true,
                            'id' => 'passwd',
                            'class' => 'form-control',
                            'placeholder' => 'Enter Current Password',
                        ]); ?>
                        <?php if (Yii::app()->user->hasFlash('error')) : ?>
                            <div class="m-2 alert alert-danger">
                                <?php echo Yii::app()->user->getFlash('error'); ?>
                            </div>
                            <script>
                                setTimeout(() => {
                                    $('.alert').fadeOut('slow')
                                }, 3000)
                            </script>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <label for="npasswd" class="col-2 h5 mt-2">New Password: </label>
                    <div class="col-10">
                        <?php echo CHtml::passwordField('npasswd', '', [
                            'id' => 'npasswd',
                            'class' => 'form-control',
                            'placeholder' => 'Enter New Password',
                        ]); ?>
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <label for="cpasswd" class="col-2 h5 mt-2">Confirm Password: </label>
                    <div class="col-10">
                        <?php echo CHtml::passwordField('cpasswd', '', [
                            'id' => 'cpasswd',
                            'class' => 'form-control',
                            'placeholder' => 'Confirm Password',
                        ]); ?>
                    </div>
                </div>
                <?php if (Yii::app()->user->hasFlash('success')) : ?>
                    <div class="m-2 alert alert-success">
                        <?php echo Yii::app()->user->getFlash('success'); ?>
                    </div>
                    <script>
                        setTimeout(() => {
                            $('.alert').fadeOut('slow')
                        }, 3000)
                    </script>
                <?php endif; ?>
                <div class="d-flex flex-row-reverse mt-3">
                    <?php echo CHtml::button(
                        'Save Changes',
                        [
                            'type' => 'submit',
                            'class' => 'btn btn-success mx-3'
                        ]
                    ) ?>
                    <button type="reset" class="btn btn-danger mx-3">Reset Changes</button>
                </div>
                <?php echo CHtml::endForm(); ?>
            </div>
        </div>
    </div>
</div>