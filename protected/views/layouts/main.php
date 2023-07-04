<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="en">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css">

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700' rel='stylesheet' type='text/css'>

	<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
	<script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>

	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->

	<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" />

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" integrity="sha256-BicZsQAhkGHIoR//IB2amPN5SrRb3fHB8tFsnqRAwnk=" crossorigin="anonymous">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

	<style>

	</style>
</head>

<body>

	<div class="container-fluid">
		<div class="row flex-nowrap">
			<div class="left-side col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-light shadow">
				<div class="d-flex flex-column align-items-center align-items-sm-start px-0 pt-2 text-white min-vh-100">
					<a href="/" class="d-flex align-items-center py-3 mb-md-0 px-2 me-md-auto text-dark text-decoration-none col-12">
						<span class="fs-5 d-none d-sm-inline">Dashboard</span>
					</a>
					<ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start col-12" id="menu">
						<li class="nav-item">
							<a href="<?= $this->createAbsoluteUrl('/home/index') ?>" class="nav-link text-dark align-middle px-3">
								<i class="fs-4 bi-house"></i> <span class="ms-1 d-none d-sm-inline">Home</span>
							</a>
						</li>
						<li>
							<a href="<?= $this->createAbsoluteUrl('/home/class') ?>" class="nav-link text-dark px-3 align-middle">
								<i class="h3 bi bi-easel2"></i> <span class="ms-1 d-none d-sm-inline">Classrooms</span>
							</a>
						</li>
						<?php if (Yii::app()->user->getState('role') == 'teacher') : ?>
							<li class="col-12">
								<a href="#teaching-submenu" data-bs-toggle="collapse" class="nav-link text-dark px-3 align-middle col-12">
									<div class="d-flex justify-content-between">
										<div>
											<i class="h3 bi bi-journal-bookmark-fill"></i> <span class="ms-1 d-none d-sm-inline">Teaching</span>
										</div>
										<div class="d-flex flex-column justify-content-center">
											<i class="h5 bi bi-chevron-down"></i>
										</div>
									</div>
								</a>
								<ul class="collapse show nav flex-column ms-1" id="teaching-submenu" data-bs-parent="#menu">
									<li class="w-100">
										<a href="/index.php/home/createclass" class="nav-link text-dark px-5">
											<i class="h5 bi bi-plus-lg"></i><span class="mx-1 d-none d-sm-inline">Create Class</span>
										</a>
										<?php
										$data = json_decode(Yii::app()->cache->get($_COOKIE['email']));
										if ($data) {
											foreach ($data->teaching as $_class) {
												$link = $this->createAbsoluteUrl('/home/class', [
													'class_id' => $_class->course_code,
													'page' => 'stream'
												]);
												echo <<< EOD
												<a href="{$link}" class="nav-link text-dark px-5">
													<i class="bi bi-ui-radios"></i> <span class="mx-1 d-none d-sm-inline">{$_class->course_name}</span>
												</a>
											EOD;
											}
										}
										?>
									</li>
								</ul>
							</li>
						<?php endif; ?>
						<li class="col-12">
							<a href="#enrolled-submenu" data-bs-toggle="collapse" class="nav-link text-dark px-3 align-middle">
								<div class="d-flex justify-content-between">
									<div>
										<!-- <i class="h3 bi bi-calendar2-week"></i> <span class="ms-1 d-none d-sm-inline">Enrolled</span> -->
										<i class="h3 bi bi-kanban"></i> <span class="ms-1 d-none d-sm-inline">Enrolled</span>
									</div>
									<div class="d-flex flex-column justify-content-center">
										<i class="h5 bi bi-chevron-down"></i>
									</div>
								</div>
							</a>
							<ul class="collapse nav flex-column ms-1" id="enrolled-submenu" data-bs-parent="#menu">
								<li class="w-100">
									<?php
									$data = json_decode(Yii::app()->cache->get($_COOKIE['email']));
									if ($data) {
										foreach ($data->enrolled as $_class) {
											$link = $this->createAbsoluteUrl('/home/class', [
												'class_id' => $_class->course_code,
												'page' => 'stream'
											]);
											echo <<< EOD
												<a href="{$link}" class="nav-link text-dark px-5">
													<i class="bi bi-ui-radios"></i> <span class="mx-1 d-none d-sm-inline">{$_class->course_name}</span>
												</a>
											EOD;
										}
									}
									?>
								</li>
							</ul>
						</li>
						<li>
							<a href="<?= $this->createAbsoluteUrl('/classwork/submission', ['page_no' => 1]) ?>" class="nav-link text-dark px-3 align-middle">
								<i class="h3 bi bi-columns-gap"></i> <span class="ms-1 d-none d-sm-inline">My Submissions</span>
							</a>
						</li>
						<li>
                            <a href="<?= $this->createAbsoluteUrl('/home/viewevent', ['class_id' => 'course-0000', 'page_no' => 1]) ?>" class="nav-link text-dark px-3 align-middle">
                                <i class="h3 bi bi-calendar3"></i> <span class="ms-1 d-none d-sm-inline">View Events</span>
                            </a>
                        </li>
						<li>
							<a href="<?= $this->createAbsoluteUrl('/today/index') ?>" class="nav-link text-dark px-3 align-middle">
								<i class="h3 bi bi-calendar2-event"></i> <span class="ms-1 d-none d-sm-inline">Today's Schedule</span>
							</a>
						</li>
					</ul>
					<hr>
					<div class="dropdown pb-4">
						<a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
							<img src="<?= Yii::app()->user->getState('image') ?>" alt="hugenerd" width="30" height="30" class="rounded-circle">
							<span class="d-none d-sm-inline mx-1"><?= isset($_COOKIE['name']) ? $_COOKIE['name'] : 'User' ?></span>
						</a>
						<ul class="dropdown-menu dropdown-menu-white text-small shadow">
							<li><a class="dropdown-item" href="/index.php/user/profile">Profile</a></li>
							<li>
								<hr class="dropdown-divider">
							</li>
							<?php
							if (!isset($_COOKIE['_id'])) {
								echo <<< END
								<li><a class="dropdown-item" href="/index.php/user/login">Sign in</a></li>
								END;
							} else {
								echo <<< END
								<li><a class="dropdown-item" href="/index.php/user/logout">Sign out</a></li>
								END;
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="col p-0">
				<div class="wrapper">
					<div>
						<div id="header" class="bg-light py-2 d-flex shadow">
							<div class="toggle-btn">
								<a class="nav-link text-dark px-3 align-middle">
									<!-- <h3><i class="bi bi-sliders2"></i></h3> -->
									<h3><i class="bi bi-list"></i></h3>
								</a>
							</div>
							<div id="logo" class="display-6 text-dark mx-2"><?php echo CHtml::encode(Yii::app()->name); ?></div>
						</div><!-- header -->
						<hr style="margin: 0px;" />
						<nav id="class-nav" class="navbar navbar-expand-lg navbar-expand-md navbar-light px-5 bg-light shadow" style="display: none;">
							<!-- <a class="navbar-brand text-dark display-6" href="/index.php/home">Home</a> -->
							<span id="page-id" class="text-white"></span>
							<div class="col-12 d-flex justify-content-between">
								<ul class="navbar-nav">
									<?php
									$options = [
										'stream' => 'Stream',
										'classwork' => 'Classwork',
										'people' => 'People',
										'schedule' => 'Schedule',
										'event' => 'Events',
										// 'meet'=>'Meeting',
									];
									$option = Yii::app()->request->getParam('page');
									$_class = Yii::app()->request->getParam('class_id');
									foreach ($options as $k => $v) {
										$link = $this->createAbsoluteUrl('/home/class', [
											'class_id' => $_class,
											'page' => $k
										]);
										if ($k == 'event') {
											$link = $this->createAbsoluteUrl('/home/viewevent', [
												'class_id' => $_class,
												'page_no' => 1
											]);
										}
										if ($option == $k) {
											echo <<< EOF
											<li class="class-nav nav-item border-bottom border-success mx-1">
												<a class="nav-link text-success" href="{$link}">$v</a>
											</li>
											EOF;
										} else {
											echo <<< EOF
											<li class="class-nav nav-item mx-1">
												<a class="nav-link text-dark" href="{$link}">$v</a>
											</li>
											EOF;
										}
									}
									?>
								</ul>
								<span class="display-6 nav-link text-dark" style="font-size: 20px;" id="class-name"></span>
							</div>
						</nav>
					</div>

					<div class="container-fluid" id="main-body-div" style="padding: 0 !important; height: 835px; overflow-y: scroll;">
						<?php echo $content; ?>
					</div><!-- page -->
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$('.toggle-btn').on('click', function(e) {
				$('.left-side').toggle(300)
			})
			$('.class-nav').click(function(e) {
				$('.class-nav').attr('class', 'class-nav nav-item')
				$('.class-nav').find('a').attr('class', 'nav-link text-dark')
				$(this).attr('class', 'class-nav nav-item border-bottom border-success')
				$(this).find('a').attr('class', 'nav-link text-success')
			})
		})
	</script>

</body>

</html>