<?php
/**
 * Logout Handler
 */

require_once __DIR__ . '/../core/App.php';
App::init();

Session::destroy();
header('Location: index.php');
exit();
