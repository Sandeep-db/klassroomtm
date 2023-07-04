<style>
    .bg-0 {
        background-image: url('https://www.gstatic.com/classroom/themes/img_read.jpg');
        background-size: cover;
    }

    .bg-1 {
        background-image: url('https://www.gstatic.com/classroom/themes/img_code.jpg');
        background-size: cover;
    }

    .bg-2 {
        background-image: url('https://gstatic.com/classroom/themes/img_bookclub.jpg');
        background-size: cover;
    }
</style>

<?php
$instuctor = $data['instructor'];
$data = $data['data'];
?>  

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
<div class="modal fade" id="new-topic-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo CHtml::beginForm('/index.php/classwork/createtopic'); ?>
            <div class="modal-body">
                <?php echo CHtml::hiddenField('course_code', Yii::app()->request->getParam('class_id')) ?>
                <label for="tname">Topic name</label>
                <?php echo CHtml::textField('name', '', [
                    'required' => true,
                    'id' => 'tname',
                    'class' => 'form-control my-2',
                    'placeholder' => 'Enter Topic Name',
                ]); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <?php echo CHtml::Button(
                    'Add Topic',
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

<!-- Vertically centered modal -->
<div class="modal fade" id="new-subtopic-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo CHtml::beginForm('/index.php/classwork/createsubtopic', 'post', [
                'enctype' => 'multipart/form-data',
            ]); ?>
            <div class="modal-body">
                <h3 class="subtopic-heading">Heading</h3>
                <?php echo CHtml::hiddenField('course_code', Yii::app()->request->getParam('class_id')) ?>
                <?php echo CHtml::hiddenField('topic_name', '') ?>
                <?php echo CHtml::hiddenField('type', '') ?>
                <label for="name">Name</label>
                <?php echo CHtml::textField('name', '', [
                    'required' => true,
                    'id' => 'name',
                    'class' => 'form-control my-2',
                    'placeholder' => 'Enter Name',
                ]); ?>
                <label for="description">Description</label>
                <?php echo CHtml::textArea('description', '', [
                    'required' => true,
                    'id' => 'description',
                    'class' => 'form-control my-2',
                    'placeholder' => 'Enter Description',
                ]); ?>
                <label for="attachments">Attachments</label>
                <?php echo CHtml::fileField('attachment[]', '', [
                    'id' => 'attachments',
                    'class' => 'form-control my-2',
                    'multiple' => 'multiple',
                ]); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <?php echo CHtml::Button(
                    'Add',
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

<!-- Vertically centered modal -->
<div class="modal fade" id="new-submission-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo CHtml::beginForm('/index.php/classwork/newsubmission', 'post', [
                'enctype' => 'multipart/form-data',
            ]); ?>
            <div class="modal-body">
                <h3 class="sub-head"></h3>
                <?php echo CHtml::hiddenField('sub_topic_id', '') ?>
                <?php echo CHtml::hiddenField('user_email', $_COOKIE['email']) ?>
                <?php echo CHtml::hiddenField('rtype', '') ?>
                <label for="sdescription">Description</label>
                <?php echo CHtml::textArea('description', '', [
                    'required' => true,
                    'id' => 'sdescription',
                    'class' => 'form-control my-2',
                    'placeholder' => 'Enter Description',
                    'rows' => 6
                ]); ?>
                <label for="sattachments">Attachments</label>
                <?php echo CHtml::fileField('attachment[]', '', [
                    'id' => 'sattachments',
                    'class' => 'form-control my-2',
                    'multiple' => 'multiple',
                ]); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <?php echo CHtml::Button(
                    'Submit',
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

<div class="p-4">
    <div class="d-flex justify-content-between">
        <div>
            <p class="display-6 filter-opt">Topics</p>
        </div>
        <?php if ($instuctor === $_COOKIE['email']) : ?>
            <div>
                <button type="button" onclick="openTopic('new-topic')" class="btn btn-success rounded-pill mx-2">
                    <h3 class="pt-2 px-2 display-6" style="font-size: 21px;">
                        <i class="bi bi-plus-lg"></i> Create
                    </h3>
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div class="pt-4">
        <?php
        $index = 0;
        $type = Yii::app()->request->getParam('filter');
        if (!$type || $type == 'topic') {
            $queryParams = [
                'class_id' => $class_id,
                'page' => 'classwork',
            ];
            foreach ($data as $instance) {
                $queryParams['topic_name'] = $instance->name;
                $date = explode("_", strval($instance->created_on));
                $topic_id = $instance->_id->{'$oid'};
                $queryParams['filter'] = 'assignment';
                $link_assignment = $this->createAbsoluteUrl('/home/class') . '?' . http_build_query($queryParams);
                $queryParams['filter'] = 'material';
                $link_material = $this->createAbsoluteUrl('/home/class') . '?' . http_build_query($queryParams);
                $queryParams['filter'] = 'question';
                $link_question = $this->createAbsoluteUrl('/home/class') . '?' . http_build_query($queryParams);
                echo <<< EOH
                    <div style="height: 400px" class="shadow-lg rounded p-5 my-4 bg-$index">
                        <div class="col-12"> 
                            <div class="d-flex justify-content-between">
                                <p class="display-6 text-light" style="font-size: 20px">Topic Name: </p>
                                <p class="h1 text-light" style="font-size: 20px">{$instance->name}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="display-6 text-light" style="font-size: 20px">Created on: </p>
                                <p class="h1 text-light" style="font-size: 20px">{$date[0]} {$date[1]}</p>
                            </div>
                        </div>
                        <hr class="text-light" />
                        <div class="d-flex flex-column-reverse" style="height: 200px">
                            <div class="d-flex justify-content-between">
                                <div class="mx-1">
                EOH;
                echo <<< EOH
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a type="button" class="btn btn-outline-light" href="$link_assignment">Assignments</a>
                EOH;
                if ($instuctor === $_COOKIE['email']) {
                    echo <<< EOH
                                        <button type="button" class="btn btn-outline-light" onclick="openModal('assignment', '{$instance->name}', '$topic_id')"><i class="bi bi-plus-lg"></i></button>
                    EOH;
                }
                echo <<< EOH
                                    </div>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a type="button" class="btn btn-outline-light" href="$link_material">Materials</a>
                EOH;
                if ($instuctor === $_COOKIE['email']) {
                    echo <<< EOH
                                        <button type="button" class="btn btn-outline-light" onclick="openModal('material', '{$instance->name}', '$topic_id')"><i class="bi bi-plus-lg"></i></button>
                    EOH;
                }
                echo <<< EOH
                                    </div>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a type="button" class="btn btn-outline-light" href="$link_question">Questions</a>
                EOH;
                if ($instuctor === $_COOKIE['email'] || true) {
                    echo <<< EOH
                                        <button type="button" class="btn btn-outline-light" onclick="openModal('question', '{$instance->name}', '$topic_id')"><i class="bi bi-plus-lg"></i></button>
                    EOH;
                }
                echo <<< EOH
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                EOH;
                $index = ($index + 1) % 3;
            }
        } else {
            function submitDiv($view_type, $sub_topic_id)
            {
                $is_teacher = false;
                $course_code = Yii::app()->request->getParam('class_id');
                $cache = json_decode(Yii::app()->cache->get($_COOKIE['email']));
                foreach ($cache->teaching as $teaching) {
                    if ($teaching->course_code === $course_code) {
                        $is_teacher = true;
                        break;
                    }
                }
                $link = Yii::app()->createAbsoluteUrl('/classwork/studentsubmissions', [
                    'sub_topic_id' => $sub_topic_id,
                    'page_no' => 1,
                ]);
                $divs = [
                    true => [
                        'assignment' => "<a class=\"btn btn-light\" href=\"$link\">View Submissions</a>",
                        'question' => "<a class=\"btn btn-light\" href=\"$link\">View Submissions</a>",
                        'material' => ""
                    ],
                    false => [
                        'assignment' => "<button class=\"btn btn-light\" onclick=\"openSubmission('assignment', '$sub_topic_id')\">Submit Assignment</button>",
                        'question' => "<button class=\"btn btn-light\" onclick=\"openSubmission('question', '$sub_topic_id')\">Submit Response</button>",
                        'material' => ""
                    ],
                ];
                return $divs[$is_teacher][$view_type];
            }
            foreach ($data as $instance) {
                $date = explode("_", strval($instance->created_on));
                $sub_topic_id = $instance->_id->{'$oid'};
                $date = substr($instance->created_on, 0, 10);
                $time = substr($instance->created_on, 11);
                $file_div = "";
                $submit_div = submitDiv($type, $sub_topic_id);
                foreach ($instance->attachments as $file) {
                    $file_div .= <<< EOF
                        <div class="btn-group m-1" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-outline-light">{$file->name}</button>
                            <button type="button" class="btn btn-outline-light"><i class="bi bi-download"></i></button>
                        </div>
                    EOF;
                }
                echo <<< EOH
                    <div style="height: 450px" class="shadow-lg rounded p-5 my-4 bg-$index">
                        <div class="col-12"> 
                            <div class="d-flex justify-content-between">
                                <p class="display-6 text-light" style="font-size: 20px">Name: </p>
                                <p class="h1 text-light" style="font-size: 20px">{$instance->name}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="display-6 text-light" style="font-size: 20px">Created on: </p>
                                <p class="h1 text-light" style="font-size: 20px"><i class="bi bi-calendar4-event"></i> {$date} <i class="bi bi-clock"></i> {$time}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <p>$submit_div</p>
                        </div>
                        <hr class="text-light" />
                        <div class="d-flex flex-column-reverse" style="height: 200px">
                            <div class="d-flex justify-content-between">
                                <div class="mx-1">
                                    $file_div
                                </div>
                            </div>
                            <p class="text-light">{$instance->description}</p>
                        </div>
                    </div>  
                EOH;
                $index = ($index + 1) % 3;
            }
        }
        ?>
    </div>
</div>

<script>
    function openModal(type, topic_name, topic_id) {
        $('form')[1].reset()
        let heading = type.charAt(0).toUpperCase() + type.slice(1) + 's'
        $('.subtopic-heading').text(heading)
        $('#type').val(type)
        $('#topic_name').val(topic_name)
        $("#new-subtopic-modal").modal('show')
    }

    function openSubmission(rtype, sub_topic_id) {
        $('form')[2].reset()
        $('#rtype').val(rtype)
        $('#sub_topic_id').val(sub_topic_id)
        $('.sub-head').text('Your Response')
        $("#new-submission-modal").modal('show')
    }

    function openTopic(id) {
        $('form')[0].reset()
        $(`#${id}-modal`).modal('show')
    }
    $(document).ready(function() {
        $('#class-nav').show()
        $('#class-name').text('<?= $class_id ?>')
        $('#main-body-div').attr('style', 'padding: 0 !important; height: 775px; overflow-y: scroll;')
        let filter_opt = '<?= Yii::app()->request->getParam('filter') ?>'
        filter_opt = filter_opt.charAt(0).toUpperCase() + filter_opt.slice(1) + 's'
        $('.filter-opt').text(filter_opt == 's' ? "Topics" : filter_opt);
    })
</script>