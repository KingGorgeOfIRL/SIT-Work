<?php
require_once 'includes/session.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('/profile/view.php');
} else {
    redirect('/login.php');
}
