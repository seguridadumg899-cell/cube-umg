<?php
require __DIR__.'/db.php';
session_destroy();
redirect('login.php');
