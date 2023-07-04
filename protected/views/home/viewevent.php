<?php
$page_no = Yii::app()->request->getParam('page_no');
if (!isset($page_no)) {
    $page_no = 1;
}
$pre_page = max(1, $page_no - 1);
if (count($events) !== 0) {
    $nxt_page = $page_no + 1;
} else {
    $nxt_page = $page_no;
}
$class_id = Yii::app()->request->getParam('class_id');
$role = Yii::app()->user->getState('role');
?>

<link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet">
<script src="https://vjs.zencdn.net/7.15.4/video.js"></script>
<script src="https://www.youtube.com/iframe_api"></script>

<?php if ($instructor === $_COOKIE['email']) : ?>
    <div class="modal fade" id="add-event" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <?php echo CHtml::beginForm('', 'post', [
                    'enctype' => 'multipart/form-data',
                    'maxFileSize' => 200 * 1024 * 1024,
                ]); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="ename" class="form-label">Name</label>
                    <?php echo CHtml::textField('name', '', [
                        'id' => 'ename',
                        'class' => 'form-control',
                        'placeholder' => 'Enter a name',
                        'required' => true,
                    ]); ?>
                    <label for="eventFile" class="form-label mt-2">Video</label>
                    <?php echo CHtml::fileField('file_link', '', [
                        'id' => 'eventFile',
                        'class' => 'form-control',
                        'required' => true,
                    ]); ?>
                </div>
                <?php echo CHtml::hiddenField('course_code', $class_id); ?>
                <?php echo CHtml::hiddenField('source', 's3-bucket'); ?>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <?php echo CHtml::Button(
                        'Upload',
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

    <div class="modal fade" id="ytb-event" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <?php echo CHtml::beginForm(''); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add YouTube Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="yname" class="form-label">Name</label>
                    <?php echo CHtml::textField('name', '', [
                        'id' => 'yname',
                        'class' => 'form-control',
                        'placeholder' => 'Enter a name',
                        'required' => true,
                    ]); ?>
                    <label for="ytbFile" class="form-label mt-2">Video</label>
                    <?php echo CHtml::textField('file_link', '', [
                        'id' => 'ytbFile',
                        'class' => 'form-control',
                        'placeholder' => 'Enter a Video ID',
                        'required' => true,
                    ]); ?>
                </div>
                <?php echo CHtml::hiddenField('course_code', $class_id); ?>
                <?php echo CHtml::hiddenField('source', 'youtube'); ?>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <?php echo CHtml::Button(
                        'Add video',
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

<div class="p-4">
    <div class="d-flex justify-content-between">
        <?php if ($instructor === $_COOKIE['email']) : ?>
            <button class="btn btn-success rounded-pill" style="height: 55px;">
                <div class="d-flex px-2" onclick="openModal()">
                    <i class="h2 bi bi-plus"></i>
                    <p class="display-6 mt-2" style="font-size: 20px;">Add Event</p>
                </div>
            </button>
        <?php endif; ?>
        <div class="d-flex">
            <div>
                <a class="btn btn-success mx-1" href="<?= Yii::app()->createAbsoluteUrl('home/viewevent', ['page_no' => $pre_page]) ?>">⥢</a>
                <a class="btn btn-outline-dark mx-1"><?= $page_no ?></a>
                <a class="btn btn-success mx-1" href="<?= Yii::app()->createAbsoluteUrl('home/viewevent', ['page_no' => $nxt_page]) ?>">⥤</a>
            </div>
            <?php if ($instructor === $_COOKIE['email']) : ?>
                <button class="btn btn-outline-dark border-0 rounded" style="height: 45px;">
                    <div class="d-flex" onclick="openYouTube()">
                        <i class="h3 bi bi-plus"></i>
                        <i class="h3 bi bi-youtube"></i>
                    </div>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="mt-4">
        <?php
        $var_div = "";
        foreach ($events as $key => $event) {
            if ($event['source'] == 's3-bucket') {
                echo <<< EOL
                    <div class="d-flex justify-content-center mt-3">
                        <video id="my-video-$key" class="video-js vjs-default-skin" controls preload="auto" width="1280" height="720">
                            <source src="{$event['file_link']['imageURL']}" type="video/mp4">
                        </video>
                        <script>
                            var player_$key = videojs('my-video-$key');
                        </script>
                    </div>
                    EOL;
            } else {
                echo <<< EOL
                    <div class="d-flex justify-content-center mt-3">
                        <div style="width: 1280px; height: 720px" id="player-$key"></div>
                    </div>
                    EOL;
                $var_div .= <<< EOL
                        new YT.Player('player-$key', {
                            videoId: '{$event['file_link']}',
                            playerVars: {
                                autoplay: 0,
                                controls: 1
                            }
                        })
                    EOL;
            }
            echo <<< EOL
                <script>
                    function onYouTubeIframeAPIReady() {
                        $var_div
                    }
                </script>
                EOL;
        }

        ?>
    </div>
</div>

<script>
    function openModal() {
        $('#add-event').modal('show')
    }

    function openYouTube() {
        $('#ytb-event').modal('show')
    }
    $(document).ready(function() {
        $('#class-nav').show()
        $('#class-name').text('<?= $class_id ?>')
        $('#main-body-div').attr('style', 'padding: 0 !important; height: 775px; overflow-y: scroll;')
    })
</script>
