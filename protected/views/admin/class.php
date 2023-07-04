<?php
$page_no = Yii::app()->request->getParam('page_no');
if (!isset($page_no)) {
    $page_no = 1;
}
$pre_page = max(1, $page_no - 1);
if (count($classes) !== 0) {
    $nxt_page = $page_no + 1;
} else {
    $nxt_page = $page_no;
}
$classes = json_encode($classes);
?>

<style>
    .bg-0 {
        background-image: url('https://www.gstatic.com/classroom/themes/img_read.jpg');
        background-size: cover;
    }

    .bg-1 {
        background-image: url('https://gstatic.com/classroom/themes/img_bookclub.jpg');
        background-size: cover;
    }

    .bg-2 {
        background-image: url('https://www.gstatic.com/classroom/themes/img_code.jpg');
        background-size: cover;
    }

    .bg-3 {
        background-image: url('https://www.gstatic.com/classroom/themes/img_breakfast.jpg');
        background-size: cover;
    }

    .bg-4 {
        background-image: url('https://www.gstatic.com/classroom/themes/img_coffee.jpg');
        background-size: cover;
    }

    .card {
        height: 350px !important;
        margin-bottom: 20px !important;
    }
</style>

<div class="p-4">
    <div class="d-flex justify-content-between mb-2">
        <p class="display-6" style="font-size: 28px !important;">All Classrooms</p>
        <div class="col-4 d-flex" style="height: 40px !important">
            <input type="text" class="form-control mx-2" name="search-class" id="search-class" onkeyup="debouncedSearch(value)" placeholder="Search Class by Id" />
            <div class="d-flex">
                <a class="btn btn-success mx-1" href="<?= Yii::app()->createAbsoluteUrl('admin/class', ['page_no' => $pre_page]) ?>">⥢</a>
                <a class="btn btn-outline-dark mx-1"><?= $page_no ?></a>
                <a class="btn btn-success mx-1" href="<?= Yii::app()->createAbsoluteUrl('admin/class', ['page_no' => $nxt_page]) ?>">⥤</a>
            </div>
        </div>
    </div>
    <div class="row" id="classes-div">
    </div>
</div>

<script>
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

    function searchClass(class_id) {
        if (class_id == '') {
            ShowClasses(JSON.parse('<?= $classes ?>'))
            return
        }
        let data = {
            'ajax': 'search-class',
            class_id: class_id,
        }
        console.log(data)
        // return
        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createAbsoluteUrl("admin/searchclass"); ?>',
            data: data,
            dataType: 'html',
            success: function(result) {
                result = JSON.parse(result)
                console.log('result', result)
                ShowClasses(result)
            },
            error: function(err) {
                console.log(err)
            },
        })
    }

    let debouncedSearch = debounce(searchClass, 500)

    function ShowClasses(data) {
        let count = 0
        $('#classes-div').empty()
        for (let _class of data) {
            let link = '<?= $this->createAbsoluteUrl('/home/class') ?>' + '?class_id=' + _class.course_code + '&page='
            $('#classes-div').append(`
            <div class="col-3">
                <div class="card">
                    <div class="bg-${(count++) % 5} py-3">
                        <div class="m-3 d-flex justify-content-between">
                            <div>
                                <p class="h4 text-light">${_class.course_name}</p>
                                <ul>
                                    <li class="text-light">Code : ${_class.course_code}</li>
                                </ul>
                            </div>
                            <div>
                                <a href="#" class="d-flex align-items-center text-dark text-decoration-none"  data-bs-toggle="dropdown" aria-expanded="false">
                                    <h3 class="text-light">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </h3>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-white text-small shadow">
                                    <li><a class="dropdown-item" href="${link + 'classwork'}">Classroom</a></li>
                                    <li><a class="dropdown-item" href="${link + 'people'}">People</a></li>
                                    <li><a class="dropdown-item" href="${link + 'schedule'}">Schedule</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Instructor: ${_class.course_instructor.name}</h5>
                            <p class="card-text">${_class.course_description}</p>
                        </div>
                        <div class="d-flex flex-row-reverse pt-3">
                            <a href="${link + 'stream'}" class="btn btn-outline-success">Open Classroom</a>
                        </div>
                    </div>
                </div>
            </div>
            `)
        }
        return true
    }
    ShowClasses(JSON.parse('<?= $classes ?>'))
</script>