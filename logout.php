<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/services/user/UserService.php';

UserService::logout();
redirect('/community-skill-exchange/login.php');
