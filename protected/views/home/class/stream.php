<style>
    .bg-0 {
        background-image: url('https://www.gstatic.com/classroom/themes/img_code.jpg');
        background-size: cover;
    }
</style>

<div class="p-4">
    <h1 class="display-6">Streams</h1>
    <?php
    function getEventCode($type) {
        return [
            'topic' => '<p class="display-6 text-dark" style="font-size: 18px">New Topic Created</p>',
            'assignment' => '<p class="display-6 text-dark" style="font-size: 18px">New Assignment Created</p>',
            'question' => '<p class="display-6 text-dark" style="font-size: 18px">New Question Created</p>',
            'material' => '<p class="display-6 text-dark" style="font-size: 18px">New Material Created</p>',
        ][$type];
    }
    foreach ($data as $stream) {
        $params = [
            'class_id' => $stream->course_code,
            'page' => 'classwork',
        ];
        if ($stream->type !== 'topic') {
            $params['topic_name'] = $stream->topic_name;
            $params['filter'] = $stream->type;
        } 
        $link = $this->createAbsoluteUrl('/home/class') . "?" . http_build_query($params);
        $date = explode("_", $stream->created_on);
        $event_div = getEventCode($stream->type);
        echo <<< EOH
            <div class="card shadow my-2 mt-4">
                <div class="bg-0 p-3">
                    <div class="d-flex justify-content-between"> 
                        <p class="display-6 text-light" style="font-size: 20px">Topic Name: </p>
                        <p class="display-6 text-light" style="font-size: 20px">{$stream->topic_name}</p>
                    </div>
                    <div class="d-flex justify-content-between"> 
                        <p class="display-6 text-light" style="font-size: 20px">Created On: </p>
                        <p class="display-6 text-light" style="font-size: 20px"><i class="bi bi-calendar4-event"></i> {$date[0]} &nbsp; <i class="bi bi-clock"></i> {$date[1]}</p>
                    </div>
                </div>
                <div class="p-3 pt-0">
                    <hr />
                    <div>
                        <div>
                            $event_div
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="$link" class="btn btn-outline-success">View</a>
                        </div>
                    </div>
                </div>
            </div>
        EOH;
    }
    ?>
</div>

<script>
    $(document).ready(function() {
        $('#class-nav').show()
        $('#class-name').text('<?= $class_id ?>')
        $('#main-body-div').attr('style', 'padding: 0 !important; height: 775px; overflow-y: scroll;')
    })
</script>