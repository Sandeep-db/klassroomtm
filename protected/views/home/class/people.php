<?php if (Yii::app()->user->hasFlash('success')) : ?>
    <div class="mx-2 alert alert-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
    <script>
        setTimeout(() => {
            $('.alert-success').fadeOut('slow')
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

<!-- Vertically centered modal -->
<?php if ($data->course_instructor->email === $_COOKIE['email']) : ?>
    <div class="modal fade" id="add-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo CHtml::beginForm('addstudent', 'post', [
                    'enctype' => 'multipart/form-data',
                ]); ?>
                <div class="modal-body">
                    <?php echo CHtml::hiddenField('course_id', Yii::app()->request->getParam('class_id')) ?>
                    <label for="email">Student Mail</label>
                    <?php echo CHtml::emailField('email', '', [
                        'id' => 'email',
                        'class' => 'form-control my-2',
                        'placeholder' => 'Enter Student Email',
                    ]); ?>
                    <p class="text-center text-dark mt-4">OR</p>
                    <label for="file">Upload an Excel</label>
                    <?php echo CHtml::fileField('file', '', [
                        'id' => 'file',
                        'class' => 'form-control my-2',
                    ]); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <?php echo CHtml::Button(
                        'Add Student(s)',
                        [
                            'type' => 'submit',
                            'class' => 'btn btn-outline-success',
                        ]
                    ); ?>
                </div>
                <?php echo CHtml::endForm(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    function showModal() {
        $('#add-modal').modal('show')
    }
</script>

<div class="p-1">
    <div class="container">
        <div class="mt-3">
            <div class="row mb-5">
                <div class="col-12 second-div">
                    <div class="row d-flex justify-content-around mt-5">
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body shadow rounded">
                                        <div class="media d-flex">
                                            <div class="media-body col-10">
                                                <p class="display-6" style="font-size: 28px;"><?php echo $data->course_students_no ?> People</p>
                                                <span class="text-success">Enrolled</span>
                                            </div>
                                            <div class="align-self-center col-2">
                                                <i class="h1 text-secondary bi bi-person"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body shadow rounded">
                                        <div class="media d-flex">
                                            <div class="media-body col-10">
                                                <p class="display-6" style="font-size: 28px;"><?php echo $data->course_instructor->name ?></p>
                                                <span class="text-success">Instructor</span>
                                            </div>
                                            <div class="align-self-center col-2">
                                                <i class="h1 text-secondary bi bi-postcard"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body shadow rounded">
                                        <div class=" d-flex">
                                            <div class="media-body col-10">
                                                <p class="display-6" style="font-size: 28px;"><?php echo $data->course_status ?></p>
                                                <span class="text-success">Status</span>
                                            </div>
                                            <div class="align-self-center col-2">
                                                <i class="h1 text-secondary bi bi-broadcast success font-large-1 float-left"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-around mt-4">
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body shadow rounded">
                                        <div class="media d-flex">
                                            <div class="media-body col-10">
                                                <p class="display-6" style="font-size: 28px;"><?php echo $data->course_code ?></p>
                                                <span class="text-success">Course Code</span>
                                            </div>
                                            <div class="align-self-center col-2">
                                                <i class="h1 text-secondary bi bi-qr-code-scan"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body shadow rounded">
                                        <div class=" d-flex">
                                            <div class="media-body col-10">
                                                <p class="display-6" style="font-size: 28px;"><?php echo $data->course_start_date ?></p>
                                                <span class="text-success">Start Date</span>
                                            </div>
                                            <div class="align-self-center col-2">
                                                <i class="h1 text-secondary bi bi-calendar-event success font-large-1 float-left"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body shadow rounded">
                                        <div class="media d-flex">
                                            <div class="media-body col-10">
                                                <p class="display-6" style="font-size: 28px;"><?php echo $data->course_end_date ?></p>
                                                <span class="text-success">End Date</span>
                                            </div>
                                            <div class="align-self-center col-2">
                                                <i class="h1 text-secondary bi bi-calendar-range warning font-large-1 float-left"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <p class="display-6" style="font-size: 30px;">Students :</p>
                <div>
                    <?php if ($data->course_instructor->email === $_COOKIE['email']) : ?>
                        <button class="btn" onclick="showModal()"><i class="h3 bi bi-person-plus text-success"></i></button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="shadow-lg rounded p-3 py-5">
                <table class="table" id="student-table">
                    <thead>
                        <tr>
                            <th scope="col" class="text-success">#</th>
                            <th scope="col" class="text-success">Name</th>
                            <th scope="col" class="text-success">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $index = 1;
                        foreach ($data->course_students as $student) {
                            echo "<tr>";
                            echo "<th scope=\"row\">$index</th>";
                            echo "<td>{$student->name}</td>";
                            echo "<td>{$student->email}</td>";
                            echo "</tr>";
                            $index++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#class-nav').show()
        $('#class-name').text('<?= $class_id ?>')
        $('#student-table').DataTable()
        $('#main-body-div').attr('style', 'padding: 0 !important; height: 775px; overflow-y: scroll;')
    })
</script>