<?php
$sub_topic_id = Yii::app()->request->getParam('sub_topic_id');
$page_no = Yii::app()->request->getParam('page_no');
$prev_page = $page_no - 1 > 0 ? $page_no - 1 : 1;
if (count($data) == 0) {
    $next_page = $page_no;
} else {
    $next_page = $page_no + 1;
}
?>

<style>
    textarea {
        resize: none;
    }
</style>

<div class="modal fade" id="summary-modal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summary-modal-Label">Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center" style="height: 100%">
                    <textarea id="summary-body" class="col-11" style="height: 100%" disabled>
                        Show  a second modal and hide this one with the button below.
                    </textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="p-4">
    <div class="d-flex">
        <a class="btn btn-outline-secondary mx-1" href="<?= $this->createAbsoluteUrl('/classwork/studentsubmissions', ['sub_topic_id' => $sub_topic_id, 'page_no' => $prev_page]) ?>"><i class="bi bi-arrow-left"></i></a>
        <div class="btn btn-success page-number mx-1"><?= $page_no ?></div>
        <a class="btn btn-outline-secondary mx-1" href="<?= $this->createAbsoluteUrl('/classwork/studentsubmissions', ['sub_topic_id' => $sub_topic_id, 'page_no' => $next_page]) ?>"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="mt-4">
        <?php
        if (count($data) > 0) {
            echo "<p class=\"display-6\" style=\"font-size: 28px\">Course Code: " . $data[0]->result->course_code . "</p>";
            echo "<p class=\"display-6 \" style=\"font-size: 28px\">Topic Name: " . $data[0]->result->topic_name . "</p>";
        } else {
            echo "<p class=\"display-6 \" style=\"font-size: 38px\">No Submissions avaliable</p>";
        }
        $index = 0;
        foreach ($data as $instance) {
            $date = explode("_", strval($instance->last_updated_on));
            $sub_topic_id = $instance->_id->{'$oid'};
            $file_div = "";
            foreach ($instance->attachments as $file) {
                $file_div .= <<< EOF
                    <div class="btn-group m-1" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-outline-dark">{$file->name}</button>
                        <button type="button" class="btn btn-outline-dark"><i class="bi bi-download"></i></button>
                    </div>
                EOF;
            }
            $bardResponse = "";
            // print_r($instance->summary);
            // echo "<br />";
            // echo "<br />";
            if (isset($instance->summary) && $instance->summary) {
                $txt = $instance->summary;
                $txt = str_replace('"', "'", $txt);
                $bardResponse = <<< EOF
                <div class="d-flex justify-content-between">
                    <p class="display-6 text-dark" style="font-size: 20px"></p>
                    <p class="display-6 text-dark" style="font-size: 20px">
                        <button class="btn btn-outline-dark" onclick="viewSummary(`{$txt}`)">View Summary</button>
                    </p>
                </div>
                EOF;
            }
            echo <<< EOH
                <div class="shadow border border-4 border-success border-top-0 border-bottom-0 rounded p-5 my-4">
                    <div class="d-flex justify-content-between">
                        <p class="display-6 text-dark" style="font-size: 20px">Submitted By: </p>
                        <p class="display-6 text-dark" style="font-size: 20px">{$instance->user_email}</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="display-6 text-dark" style="font-size: 20px">Submitted on: </p>
                        <div class="d-flex">
                            <p class="display-6 text-dark" style="font-size: 20px">
                                <i class="bi bi-calendar4-event"></i> {$date[0]} <i class="bi bi-clock"></i> {$date[1]}
                            </p>
                        </div>
                    </div>
                    $bardResponse
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

<script>
    $(document).ready(function() {
        let filter = '<?= Yii::app()->request->getParam('rtype') ?>'
        if (!filter || filter === '') {
            $('.all-btn').attr('class', 'btn btn-dark all-btn')
        } else {
            $(`.${filter}-btn`).attr('class', `btn btn-dark ${filter}-btn`)
        }
    })

    function viewSummary(data) {
        $('#summary-body').val(data)
        $('#summary-modal').modal('show')
    }
</script>