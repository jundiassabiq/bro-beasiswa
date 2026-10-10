<?php
require_once __DIR__ . '/sesi.php';
redirect(loginAktif() ? 'daftar.php' : 'login.php');
