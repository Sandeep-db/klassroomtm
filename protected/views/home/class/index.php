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

    /* .nav- */
</style>

<div class="p-4">
    <p class="display-6" style="font-size: 28px !important;">Teaching Classrooms</p>
    <div class="row" id="teaching-classes-div">
    </div>
    <hr />
    <p class="display-6" style="font-size: 28px !important;">Enrolled Classrooms</p>
    <div class="row" id="enrolled-classes-div">
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
        $('#teaching-classes-div').empty()
        for (let _class of teaching) {
            let link = '<?= $this->createAbsoluteUrl('/home/class') ?>' + '?class_id=' + _class.course_code + '&page=stream'
            $('#teaching-classes-div').append(`
            <div class="col-4">
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
                                    <li><a class="dropdown-item" href="#">Mark as Completed</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Instructor: ${name}</h5>
                            <p class="card-text">${_class.course_description}</p>
                        </div>
                        <div class="d-flex flex-row-reverse pt-3">
                            <a href="${link}" class="btn btn-outline-success">Open Classroom</a>
                        </div>
                    </div>
                </div>
            </div>
            `)
        }
        $('#enrolled-classes-div').empty()
        for (let _class of enrolled) {
            let link = '<?= $this->createAbsoluteUrl('/home/class') ?>' + '?class_id=' + _class.course_code + '&page=stream'
            $('#enrolled-classes-div').append(`
            <div class="col-4">
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
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Instructor: ${_class.course_instuctor_name}</h5>
                            <p class="card-text">${_class.course_description}</p>
                        </div>
                        <div class="d-flex flex-row-reverse pt-3">
                            <a href="${link}" class="btn btn-outline-success">Open Classroom</a>
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