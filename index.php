<?php
require __DIR__ . '/config/helper.php';

if (user_login()) {
    redirect(beranda_role($_SESSION['user']['role']));
}
redirect(BASE_URL . '/auth/login.php');
