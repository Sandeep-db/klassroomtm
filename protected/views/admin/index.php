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

    .Link:hover {
        background-color: #08546C;
        opacity: 0.5;
        color: lightblue;
    }

    .card {
        height: 200px !important;
        margin-bottom: 20px !important;
    }

    a, i {
        text-decoration: none;
        cursor: pointer;
        color: #08546C;
    }

    a:hover {
        color: lightblue;
    }
</style>

<div class="p-4">
    <h1 class="display-6 mb-3">Admin</h1>
    <div class="row">
        <div class="col-6">
            <div class="shadow-lg rounded p-4 my-3 mx-1 d-flex justify-content-between Link">
                <a href="<?= $this->createAbsoluteUrl('/admin/class', ['page_no' => 1]) ?>" class="display-6" style="font-size: 30px;">View Classes</a>
                <i class="h1 bi bi-arrow-right"></i>
            </div>
        </div>
        <div class="col-6">
            <div class="shadow-lg rounded p-4 my-3 mx-1 d-flex justify-content-between Link">
                <a href="<?= $this->createAbsoluteUrl('/admin/users', ['page_no' => 1]) ?>" class="display-6" style="font-size: 30px;">View Users</a>
                <i class="h1 bi bi-arrow-right"></i>
            </div>
        </div>
        <div class="col-6">
            <div class="shadow-lg rounded p-4 mb-3 mx-1 d-flex justify-content-between Link">
                <a href="<?= $this->createAbsoluteUrl('/home/createclass') ?>" class="display-6" style="font-size: 30px;">Create Class</a>
                <i class="h1 bi bi-arrow-right"></i>
            </div>
        </div>
        <div class="col-6">
            <div class="shadow-lg rounded p-4 mb-3 mx-1 d-flex justify-content-between Link">
                <a href="<?= $this->createAbsoluteUrl('/admin/viewevent', ['page_no' => 1]) ?>" class="display-6" style="font-size: 30px;">Upload Events</a>
                <i class="h1 bi bi-arrow-right"></i>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="card shadow-lg bg-0">
                <div class="py-3">
                    <div class="m-3 d-flex justify-content-between">
                        <div>
                            <p class="h4 text-light">Total Classes</p>
                        </div>
                    </div>
                </div>
                <hr class="text-light" />
                <div class="card-body d-flex flex-column justify-content-end">
                    <div>
                        <h5 class="card-title text-light">Total: <?= $data['total_classes'] ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow-lg bg-2">
                <div class="py-3">
                    <div class="m-3 d-flex justify-content-between">
                        <div>
                            <p class="h4 text-light">Finished Classes</p>
                        </div>
                    </div>
                </div>
                <hr class="text-light" />
                <div class="card-body d-flex flex-column justify-content-end">
                    <div>
                        <h5 class="card-title text-light">Total: <?= $data['finshed_classes'] ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow-lg bg-4">
                <div class="py-3">
                    <div class="m-3 d-flex justify-content-between">
                        <div>
                            <p class="h4 text-light">Start Not Classes</p>
                        </div>
                    </div>
                </div>
                <hr class="text-light" />
                <div class="card-body d-flex flex-column justify-content-end">
                    <div>
                        <h5 class="card-title text-light">Total: <?= $data['notstarted_classes'] ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card shadow-lg bg-3">
                <div class="py-3">
                    <div class="m-3 d-flex justify-content-between">
                        <div>
                            <p class="h4 text-light">Instructors</p>
                        </div>
                    </div>
                </div>
                <hr class="text-light">
                <div class="card-body d-flex flex-column justify-content-end">
                    <div>
                        <h5 class="card-title text-light">Total: <?= $data['teachers'] ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card shadow-lg bg-1">
                <div class="py-3">
                    <div class="m-3 d-flex justify-content-between">
                        <div>
                            <p class="h4 text-light">Students</p>
                        </div>
                    </div>
                </div>
                <hr class="text-light">
                <div class="card-body d-flex flex-column justify-content-end">
                    <div>
                        <h5 class="card-title text-light">Total: <?= $data['students'] ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".card").hover(
            function() {
                $(this).addClass('shadow-lg').css('cursor', 'pointer')
            },
            function() {
                $(this).removeClass('shadow-lg')
            }
        )

        $(".Link").hover(
            function() {
                $(this).find('a, i').css('color', 'white')
            },
            function() {
                $(this).find('a, i').css('color', '#08546C')
            }
        )
    })
</script>