<?php
$rtype = Yii::app()->request->getParam('rtype');
$page_no = Yii::app()->request->getParam('page_no');
$prev_page = $page_no - 1 > 0 ? $page_no - 1 : 1;
if (count($data) == 0) {
    $next_page = $page_no;
} else {
    $next_page = $page_no + 1;
}
?>

<div class="p-4">
    <div class="d-flex justify-content-between">
        <div class="btn-group" role="group" aria-label="Basic example">
            <a type="button" class="btn btn-outline-dark all-btn" href="<?= $this->createAbsoluteUrl('/classwork/submission', ['page_no' => 1]) ?>">All</a>
            <a type="button" class="btn btn-outline-dark assignment-btn" href="<?= $this->createAbsoluteUrl('/classwork/submission', ['rtype' => 'assignment', 'page_no' => 1]) ?>">Assignments</a>
            <a type="button" class="btn btn-outline-dark question-btn" href="<?= $this->createAbsoluteUrl('/classwork/submission', ['rtype' => 'question', 'page_no' => 1]) ?>">Questions</a>
        </div>
        <div class="d-flex">
            <a class="btn btn-outline-secondary mx-1" href="<?= $this->createAbsoluteUrl('/classwork/submission', ['rtype' => $rtype, 'page_no' => $prev_page]) ?>"><i class="bi bi-arrow-left"></i></a>
            <div class="btn btn-success page-number mx-1"><?= $page_no ?></div>
            <a class="btn btn-outline-secondary mx-1" href="<?= $this->createAbsoluteUrl('/classwork/submission', ['rtype' => $rtype, 'page_no' => $next_page]) ?>"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="mt-5">
        <?php
        $index = 0;
        foreach ($data as $instance) {
            $date = explode("_", strval($instance->last_updated_on));
            $submission_id = $instance->_id->{'$oid'};
            $file_div = "";
            $delete_path = "nothing_to_delete";
            if (count($instance->attachments) > 0) {
            }
            foreach ($instance->attachments as $file) {
                $delete_path = explode("/", $file->imageURL);
                array_pop($delete_path);
                $delete_path = join("/", $delete_path);
                $file_div .= <<< EOF
                    <div class="btn-group m-1" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-outline-dark">{$file->name}</button>
                        <button type="button" class="btn btn-outline-dark"><i class="bi bi-download"></i></button>
                    </div>
                EOF;
            }
            echo <<< EOH
                <div id="$submission_id" class="p-5 my-4 rounded border border-5 border-top-0 border-bottom-0 shadow border-success">
                    <div class="d-flex justify-content-between">
                        <p class="display-6 text-dark" style="font-size: 20px">Submitted on: </p>
                        <div class="d-flex">
                            <p class="display-6 text-dark" style="font-size: 20px">
                                <i class="bi bi-calendar4-event"></i> {$date[0]} <i class="bi bi-clock"></i> {$date[1]}
                                <div style="margin-left: 20px">
                                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none"  data-bs-toggle="dropdown" aria-expanded="false">
                                        <h4 class="text-dark">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </h4>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-white text-small shadow">
                                        <li><a class="dropdown-item" onclick="deleteSubmission('$submission_id', '$delete_path')">Delete</a></li>
                                    </ul>
                                </div>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="display-6 text-dark" style="font-size: 20px">Class Id: </p>
                        <p class="display-6 text-dark" style="font-size: 20px">{$instance->result->course_code}</p> 
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="display-6 text-dark" style="font-size: 20px">Topic Name: </p>
                        <p class="display-6 text-dark" style="font-size: 20px">{$instance->result->topic_name}</p> 
                    </div>
                    <hr class="text-dark" />
                    <div class="d-flex flex-column-reverse">
                        <div class="d-flex justify-content-between">
                            <div class="mx-1">
                                $file_div
                            </div>
                        </div>
                        <p class="text-dark">{$instance->description}</p>
                    </div>
                </div>  
            EOH;
            $index = ($index + 1) % 3;
        }
        ?>
    </div>
</div>

<script type="text/javascript">
    function deleteSubmission(_id, delete_path) {
        let data = {
            'ajax': 'delete-submission',
            '_id': _id,
            'delete_path': delete_path,
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createAbsoluteUrl("classwork/deletesubmission"); ?>',
            data: data,
            dataType: 'html',
            success: function(result) {
                result = JSON.parse(result)
                console.log({
                    result
                })
                let rem_id = result._id
                $(`#${rem_id}`).remove()
            },
            error: function(err) {
                console.log(err)
            },
        })
    }

    $(document).ready(function() {
        let filter = '<?= Yii::app()->request->getParam('rtype') ?>'
        if (!filter || filter === '') {
            $('.all-btn').attr('class', 'btn btn-dark all-btn')
        } else {
            $(`.${filter}-btn`).attr('class', `btn btn-dark ${filter}-btn`)
        }
    })
</script>