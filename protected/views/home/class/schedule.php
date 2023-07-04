<?php
$instructor = $data['instructor'];
$data = $data['data'];
$course_code = Yii::app()->request->getParam('class_id');
?>

<div class="modal fade" id="delete-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                <button type="button" class="btn close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Do you want to delete the Schedule
            </div>
            <?= CHtml::beginForm('/index.php/classwork/addschedule', 'post', array('id' => 'schedule-form')); ?>
            <?= CHtml::hiddenField('course_code', Yii::app()->request->getParam('class_id')) ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <?= CHtml::Button(
                    'Confirm Delete',
                    [
                        'type' => 'submit',
                        'class' => 'btn btn-danger',
                    ]
                ); ?>
            </div>
            <input type="text" name="_id" id="d_id" hidden />
            <?= CHtml::hiddenField('operation', 'delete') ?>
            <?= CHtml::endForm(); ?>
        </div>
    </div>
</div>

<div class="p-4">
    <div class="d-flex justify-content-between">
        <div>
            <p class="display-6 filter-opt">Schedule</p>
        </div>
        <?php
        if ($instructor === $_COOKIE['email']) {
            echo <<< EOH
                <div>
                    <button type="button" onclick="createSchedule()" class="btn btn-success rounded-pill mx-2">
                        <h3 class="pt-2 px-2 display-6" style="font-size: 21px;">
                            <i class="bi bi-plus-lg"></i> Create
                        </h3>
                    </button>
                </div>
                EOH;
        }
        ?>
    </div>
    <div>
        <?php
        if (Yii::app()->user->hasFlash('scheduleerror')) {
            echo '<div class="alert alert-danger" role="alert">';
            echo Yii::app()->user->getFlash('scheduleerror');
            echo '</div>';
        }
        ?>
    </div>
    <div class="modal fade" id="new-schedule-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?= CHtml::beginForm('/index.php/classwork/addschedule', 'post', array('id' => 'schedule-form')); ?>
                <?= CHtml::hiddenField('course_code', Yii::app()->request->getParam('class_id')) ?>

                <div class="modal-body ">
                    <div class="d-flex justify-content-center my-5">
                        <div>
                            <div class="m-1">
                                <label for="startdate" style="width: 100px">Start Date: </label>
                                <?php
                                $defaultStarDate = date('Y-m-d');
                                echo CHtml::dateField('startdate', $defaultStarDate, [
                                    'required' => true,
                                    'id' => 'startdate',
                                ]);
                                ?>
                            </div>
                            <div class="m-1">
                                <label for="enddate" style="width: 100px">End Date: </label>
                                <?php
                                $defaultEndDate = date('Y-m-d');
                                echo CHtml::dateField('enddate', $defaultEndDate, [
                                    'required' => true,
                                    'id' => 'enddate',
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="p-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Topic</th>
                                    <th>Learning Outcomes</th>
                                    <th>Hours/Time</th>
                                </tr>
                            </thead>
                            <tbody id="schedule">
                                <tr>
                                    <td><?= CHtml::textField('topic[]', ''); ?></td>
                                    <td><?= CHtml::textArea('learningOutcome[]', '', array('name' => 'learning_outcome[]', 'rows' => 1, 'cols' => 40)); ?></td>
                                    <td><?= CHtml::textField('hours[]', '', array('name' => 'hours[]')); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center mb-3">
                    <div id="addrow" class="btn btn-success">Add Row</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <?= CHtml::Button(
                        'Submit',
                        [
                            'type' => 'submit',
                            'class' => 'btn btn-outline-success',
                        ]
                    ); ?>
                </div>
                <?= CHtml::hiddenField('_id', '') ?>
                <?= CHtml::hiddenField('operation', '') ?>
                <?= CHtml::endForm(); ?>

            </div>
        </div>
    </div>
</div>

<div id="accordion">
    <?php
    $index = 0;
    $today_date = date("Y-m-d");
    ?>
    <?php foreach ($data as $index => $schedule) : ?>
        <?php $flag = $schedule->startdate <= $today_date && $today_date <= $schedule->enddate ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?= $index + 1; ?>">
                <button class="accordion-button bg-light text-dark <?= $flag ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index + 1; ?>" aria-expanded="false" aria-controls="collapse<?= $index + 1; ?>">
                    <?= $schedule->startdate; ?>
                    <span class="mx-2">-</span>
                    <?= $schedule->enddate; ?>
                </button>
            </h2>
            <div id="collapse<?= $index + 1; ?>" class="accordion-collapse <?= $flag ? 'collapsed' : 'collapse' ?>" aria-labelledby="heading<?= $index + 1; ?>" data-bs-parent="#accordion">
                <div class="accordion-body">
                    <table class="table" id="schedule_<?= $index ?>">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Learning Outcomes</th>
                                <th>Hours/Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < count($schedule->topic); $i++) : ?>
                                <tr>
                                    <td><?= $schedule->topic[$i]; ?></td>
                                    <td><?= $schedule->learningOutcome[$i]; ?></td>
                                    <td><?= $schedule->hours[$i]; ?></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <?php
                    if ($instructor === $_COOKIE['email']) {
                        $_id = $schedule->_id->{'$oid'};
                        echo <<< EOH
                            <div class = "d-flex justify-content-end">
                                <div>
                                <button class="btn btn-warning" onclick="editSchedule($index, '{$schedule->startdate}', '{$schedule->enddate}', '{$_id}')">Edit</button>
                                </div>
                                <span class="mx-2"></span>
                                <div>
                                    <button class="btn btn-danger" onclick="deleteSchedule($index, '{$_id}')">Delete</button>
                                </div>
                            </div>
                        EOH;
                    }
                    ?>
                </div>
            </div>

        </div>
    <?php endforeach; ?>

</div>

<script>
    function createSchedule() {
        $('form')[0].reset()
        $(`#new-schedule-modal`).modal('show')
        $('#addrow').on('click', function(e) {
            e.preventDefault()
            let newRow = $(`
                <tr>
                    <td><?= CHtml::textField("topic[]", ""); ?></td>
                    <td><?= CHtml::textArea("learningOutcome[]", "", ["name" => "learning_outcome[]", "rows" => 1, "cols" => 40]); ?></td>
                    <td><?= CHtml::textField("hours[]", "", ["name" => "hours[]"]); ?></td>
                </tr>
            `);
            $('#schedule').append(newRow)
        })
    }

    function editSchedule(index, startdate, enddate, id) {
        console.log(startdate, enddate)
        let data = $('#schedule_' + index).find('tr').map((i, v) => {
            let k = $(v).find('td').map((j, td) => {
                return $(td).text()
            }).get()
            return [k]
        }).get()
        data.shift()
        $('form')[0].reset()
        $(`#new-schedule-modal`).modal('show')
        $('#schedule').empty()
        $('#startdate').val(startdate)
        $('#enddate').val(enddate)
        for (let i = 0; i < data.length; ++i) {
            let newRow = $(`
                <tr>
                    <td><?= CHtml::textField("topic[]", ""); ?></td>
                    <td><?= CHtml::textArea("learningOutcome[]", "", ["name" => "learning_outcome[]", "rows" => 1, "cols" => 40]); ?></td>
                    <td><?= CHtml::textField("hours[]", "", ["name" => "hours[]"]); ?></td>
                </tr>
            `);
            $('#schedule').append(newRow)
        }
        $('#addrow').on('click', function(e) {
            e.preventDefault()
            let newRow = $(`
                <tr>
                    <td><?= CHtml::textField("topic[]", ""); ?></td>
                    <td><?= CHtml::textArea("learningOutcome[]", "", ["name" => "learning_outcome[]", "rows" => 1, "cols" => 40]); ?></td>
                    <td><?= CHtml::textField("hours[]", "", ["name" => "hours[]"]); ?></td>
                </tr>
            `);
            $('#schedule').append(newRow)
        })
        $('#_id').val(id)
        setTimeout(() => {
            $('input[name="topic[]"]').map((i, v) => {
                $(v).val(data[i][0])
            })
            $('textarea[name="learningOutcome[]"]').map((i, v) => {
                $(v).val(data[i][1])
            })
            $('input[name="hours[]"]').map((i, v) => {
                $(v).val(data[i][2])
            })
        }, 100)
    }

    function deleteSchedule(index, id) {
        console.log(index)
        $('#d_id').val(id)
        $('#delete-model').modal('show')
        $('#operation').val('delete')
    }

    $(document).ready(function() {
        $('#class-nav').show()
        $('#class-name').text('<?= $class_id ?>')
        $('#main-body-div').attr('style', 'padding: 0 !important; height: 775px; overflow-y: scroll;')

        setTimeout(function() {
            $('.alert').fadeOut('slow')
        }, 5000)
    })
</script>