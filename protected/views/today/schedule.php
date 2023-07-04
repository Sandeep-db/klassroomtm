<style>
    div.fixed {
        position: fixed;
        bottom: 0;
        right: 0;
    }

    *::-webkit-scrollbar {
        display: none;
    }

    textarea {
        resize: none;
    }

    textarea:focus,
    input[type="text"]:focus,
    input[type="password"]:focus,
    input[type="datetime"]:focus,
    input[type="datetime-local"]:focus,
    input[type="date"]:focus,
    input[type="month"]:focus,
    input[type="time"]:focus,
    input[type="week"]:focus,
    input[type="number"]:focus,
    input[type="email"]:focus,
    input[type="url"]:focus,
    input[type="search"]:focus,
    input[type="tel"]:focus,
    input[type="color"]:focus,
    .uneditable-input:focus {
        border-color: rgba(126, 239, 104, 0.8);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(126, 239, 104, 0.6) !important;
        outline: 0 none;
    }

    .left {
        border-radius: 0px 20px 20px 20px;
    }

    .right {
        border-radius: 20px 0px 20px 20px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/ace-builds@1.4.12/src/ace.js"></script>

<div class="p-4">
    <?php if (isset($data->startdate)) : ?>
        <p class="display-6" style="font-size: 26px;">Class id: <?= Yii::app()->request->getParam('class_id') ?></p>
        <div class="mt-5">
            <div class="d-flex">
                <p class="display-6" style="font-size: 22px;">Today: <?= date('d M Y') ?></p>
            </div>
            <div>
                <p class="display-6" style="font-size: 24px;">Schedule: </p>
                <div class="d-flex col-4 justify-content-between">
                    <p class="display-6" style="font-size: 22px;">From: <?= $data->startdate ?></p>
                    <p class="display-6" style="font-size: 22px;">To: <?= $data->enddate ?></p>
                </div>
            </div>
            <table class="table table-secondary table-striped table-hover rounded shadow-lg" id="schedule-table">
                <thead>
                    <tr>
                        <th>Topic</th>
                        <th>Learning Outcomes</th>
                        <th>Hours/Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($data->topic); $i++) : ?>
                        <tr>
                            <td><?php echo $data->topic[$i]; ?></td>
                            <td><?php echo $data->learningOutcome[$i]; ?></td>
                            <td><?php echo $data->hours[$i]; ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div>
            <p class="display-6 text-center">No avaliable schedule today in <?= Yii::app()->request->getParam('class_id') ?></p>
        </div>
    <?php endif; ?>
    <?php if (false) : ?>
        <div class="fixed p-5">
            <div class="chat shadow-lg rounded p-3 bg-dark mx-3 mb-3" style="width: 500px !important; height: 650px;">
                <div class="message-log overflow-auto mb-3 rounded" style="height: 530px;">
                </div>
                <div class="btn-group btn-group-lg col-12" role="group" aria-label="chat-bottom">
                    <textarea class="form-control" placeholder="Type Message ..." name="message" id="message"></textarea>
                    <button type="button" class="btn btn-success" onclick="sendQuery()"><i class="h3 bi bi-send text-light"></i></button>
                </div>
            </div>
            <div>
                <div class="border border-success rounded-circle p-2" style="width: 65px !important">
                    <img src="../../../images/bot-image.png" onclick="$('.chat').toggle(500)" alt="bot-image">
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function getChatDiv(type, text) {
        let uuid = Date.now()
        let divs = [
            `
            <div class="d-flex m-2">
                <div class="col-10">
                    <div style="height: 300px !important" class="text-light border border-3 border-success left p-2" id="${uuid}"></div>
                </div>
            </div>
            `,
            `
            <div class="d-flex justify-content-end m-2">
                <div class="col-10">
                    <p class="text-light text-right border border-2 border-light right p-2" id="${uuid}">${text}</p>
                </div>
            </div>
            `
        ]
        return {
            div: divs[type],
            uuid: uuid
        }
    }

    function chatBot(query) {
        let data = {
            'query': query,
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createAbsoluteUrl("today/ping"); ?>',
            data: data,
            dataType: 'html',
            success: function(result) {
                console.log({
                    result
                })
                result = JSON.parse(result)
                let {
                    div,
                    uuid
                } = getChatDiv(0, '')
                result = result.replace(/(?:\\r\\n|\\r|\\n)/g, "\n");
                $('.message-log').append(div)
                setInterval(() => {
                    let botReply = document.getElementById(uuid)
                    let editor = ace.edit(botReply)
                    editor.setTheme('ace/theme/monokai')
                    editor.session.setMode('ace/mode/python')
                    editor.setValue(result)
                    editor.setReadOnly(true)
                }, 100)
            },
            error: function(err) {
                console.log(err)
            },
        })
    }

    function sendQuery() {
        let query = $('#message').val()
        $('#message').val('')
        let {
            div,
            uuid
        } = getChatDiv(1, query)
        $('.message-log').append(div)
        console.log(query)
        chatBot(query)
    }

    $(document).ready(function() {
        $('.chat').hide()
        $('.message-log').empty()
    })
</script>