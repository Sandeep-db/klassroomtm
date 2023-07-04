<?php
$page_no = Yii::app()->request->getParam('page_no');
if (!isset($page_no)) {
    $page_no = 1;
}
$pre_page = max(1, $page_no - 1);
if (count($users) !== 0) {
    $nxt_page = $page_no + 1;
} else {
    $nxt_page = $page_no;
}
?>

<script>
    function showModal(_id, email, name) {
        $('#_id-label').text(_id)
        $('#email-label').val(email)
        $('#name-label').val(name)
        $('#update-modal').modal('show')
    }

    function deleteModal(_id, email, name, role) {
        $('#delete-id').text(_id)
        $('#delete-email').text("Email : " + email)
        $('#delete-name').text("Name : " + name)
        $('#delete-role').text("Role : " + role)
        $('#delete-modal').modal('show')
    }
</script>

<!-- Modal -->
<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">user id: <span id="delete-id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo CHtml::tag('p', ['id' => 'delete-name'], ''); ?>
                <?php echo CHtml::tag('p', ['id' => 'delete-email'], ''); ?>
                <?php echo CHtml::tag('p', ['id' => 'delete-role'], ''); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger">Confirm Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">user id: <span id="_id-label"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex">
                    <div class="col-3 d-flex flex-column justify-content-center">
                        <label class="form-label" for="name-label">Name: </label>
                    </div>
                    <div class="col-9">
                        <input class="form-control my-2" id="name-label" disabled=true />
                    </div>
                </div>
                <div class="d-flex">
                    <div class="col-3 d-flex flex-column justify-content-center">
                        <label class="form-label" for="email-label">Email: </label>
                    </div>
                    <div class="col-9">
                        <input class="form-control my-2" id="email-label" disabled=true />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-success">Confirm to Promote</button>
            </div>
        </div>
    </div>
</div>

<div class="p-4">
    <div class="d-flex justify-content-between mb-2">
        <div>
            <a class="btn btn-success mx-1" href="<?= Yii::app()->createAbsoluteUrl('admin/users', ['page_no' => $pre_page]) ?>">⥢</a>
            <a class="btn btn-outline-dark mx-1"><?= $page_no ?></a>
            <a class="btn btn-success mx-1" href="<?= Yii::app()->createAbsoluteUrl('admin/users', ['page_no' => $nxt_page]) ?>">⥤</a>
        </div>
        <div class="col-3">
            <input class="form-control" type="text" name="user-name" onkeyup="debouncedSearch(value)" placeholder="Search by name" />
        </div>
    </div>
    <div class="row users-div">
        <?php
        $json_user = json_encode($users);
        foreach ($users as $user) {
            if ($user['role'] == 'admin') {
                continue;
            }
            echo <<<EOF
            <div class="col-4">
                <div class="shadow rounded m-2 p-2 pt-4 col-12 d-flex justify-content-around">
                    <div class="d-flex flex-column justify-content-center">
                        <p>Name : {$user['name']}</p>
                        <p>Email : {$user['email']}</p>
                        <p>Role : {$user['role']}</p>
                    </div>
                    <div class="d-flex flex-column justify-content-center">
            EOF;
            $redirect = $this->createAbsoluteUrl('admin/user');
        ?>
            <?php echo CHtml::beginForm('user', 'post'); ?>
            <?php echo CHtml::hiddenField('user_mail', $user['email']); ?>
            <?php echo CHtml::submitButton('view', [
                'class' => 'btn btn-outline-secondary my-1 col-12',
            ]) ?>
            <?php echo CHtml::endForm() ?>
        <?php
            if ($user['role'] == 'teacher') {
                echo CHtml::Button('Promoted', [
                    'class' => 'btn btn-success my-1',
                    'disabled' => 'true',
                ]);
            } else {
                echo CHtml::Button('Promote', [
                    'class' => 'btn btn-outline-success my-1',
                    'onclick' => 'showModal("' . $user['_id'] . '", "' . $user['email'] . '", "' . $user['name'] . '")'
                ]);
            }
            echo CHtml::Button('delete', [
                'class' => 'btn btn-outline-danger my-1',
                'onclick' => "deleteModal('{$user['_id']}', '{$user['email']}', '{$user['name']}', '{$user['role']}')"
            ]);
            echo <<<EOF
                    </div>
                </div>
            </div>
            EOF;
        }
        ?>
    </div>
</div>

<script type="text/javascript">
    function rebuildPage(users) {
        $('.users-div').empty()
        for (let user of users) {
            if (user.role === 'admin') {
                continue
            }
            let promote = `<input class="btn btn-success my-1" type="button" value="Promoted" disabled=true />`
            if (user.role == 'student') {
                promote = `<input class="btn btn-outline-success my-1" onclick="showModal('${user._id.$oid}', '${user.email}', '${user.name}')" name="yt7" type="button" value="Promote" />`
            }
            let div = `
            <div class="col-4">
                <div class="shadow rounded m-2 p-2 pt-4 col-12 d-flex justify-content-around">
                    <div class="d-flex flex-column justify-content-center">
                        <p>Name : ${user.name}</p>
                        <p>Email : ${user.email}</p>
                        <p>Role : ${user.role}</p>
                    </div>
                    <div class="d-flex flex-column justify-content-center">            
                        <form action="user" method="post">            
                            <input type="hidden" value="${user.email}" name="user_mail" id="user_mail" />
                            <input class="btn btn-outline-secondary my-1 col-12" type="submit" name="yt6" value="view" />            
                        </form>        
                        ${promote}
                        <input class="btn btn-outline-danger my-1" onclick="deleteModal('${user._id.$oid}', '${user.email}', '${user.name}', '${user.role}')" name="yt8" type="button" value="delete" />
                    </div>
                </div>
            </div>`
            $('.users-div').append(div)
        }
    }

    function searchUser(name) {
        if (name == '') {
            rebuildPage(JSON.parse('<?= $json_user ?>'))
            return
        }
        let data = {
            'ajax': 'search-user',
            name: name,
        }
        console.log(data)
        // return
        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createAbsoluteUrl("admin/searchusers"); ?>',
            data: data,
            dataType: 'html',
            success: function(result) {
                result = JSON.parse(result)
                console.log('result', result)
                rebuildPage(result)
            },
            error: function(err) {
                console.log(err)
            },
        })
    }

    function debounce(fn, delay) {
        this.time_id = null
        return function(...args) {
            if (this.time_id) {
                clearInterval(this.time_id)
            }
            this.time_id = setTimeout(() => {
                fn(...args)
                this.time_id = null
            }, delay)
        }
    }

    let debouncedSearch = debounce(searchUser, 500)
</script>