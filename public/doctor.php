<?php
require_once dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/view-helpers.php';
require_once dirname(__DIR__) . '/src/AuthService.php';
require_once dirname(__DIR__) . '/src/RequestRepository.php';
require_once dirname(__DIR__) . '/src/DoctorController.php';
$db = Database::connect();
$controller = new DoctorController(new AuthService($db), new RequestRepository($db));
extract($controller->handle(), EXTR_SKIP);
require dirname(__DIR__) . '/templates/page.php';
