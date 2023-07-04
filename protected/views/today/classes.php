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

    .card {
        margin-bottom: 20px !important;
    }

    /* .nav- */
</style>

<div class="p-4">
    <p class="display-6" style="font-size: 28px !important;">Classrooms</p>
    <div class="row" id="classes-div">
    </div>
</div>

<script>
    function getToLink(link) {
        console.log(link)
        location.href = link
    }

    function ShowClasses(data) {
        let count = 0
        let name = '<?= $_COOKIE['name'] ?>'
        let teaching = data.teaching
        let enrolled = data.enrolled
        $('#classes-div').empty()
        for (let _class of teaching) {
            let link = '<?= $this->createAbsoluteUrl('/today/schedule') ?>' + '?class_id=' + _class.course_code
            $('#classes-div').append(`
            <div class="col-6">
                <div class="card">
                    <div class="bg-${(count++) % 3} py-3">
                        <div class="m-3 d-flex justify-content-between">
                            <div>
                                <p class="h4 text-light">${_class.course_name}</p>
                                <ul>
                                    <li class="text-light">Code : ${_class.course_code}</li>
                                </ul>
                            </div>
                            <div>
                                <a href="${link}" class="btn btn-light">View Today's Schedule</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `)
        }
        for (let _class of enrolled) {
            let link = '<?= $this->createAbsoluteUrl('/today/schedule') ?>' + '?class_id=' + _class.course_code
            $('#classes-div').append(`
            <div class="col-6">
                <div class="card">
                    <div class="bg-${(count++) % 3} py-3">
                        <div class="m-3 d-flex justify-content-between">
                            <div>
                                <p class="h4 text-light">${_class.course_name}</p>
                                <ul>
                                    <li class="text-light">Code : ${_class.course_code}</li>
                                </ul>
                            </div>
                            <div>
                                <a href="${link}" class="btn btn-light">View Today's Schedule</a>
                            </div>
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