<?php
// app/admin/controllers/logout.php
session_destroy();
redirect('/admin/login');
