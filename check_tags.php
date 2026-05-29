<?php
$c = file_get_contents('f:/CAIDAT/laragon/www/bida/resources/views/admin/dashboard.blade.php');
echo "div open: " . substr_count($c, '<div') . "\n";
echo "div close: " . substr_count($c, '</div') . "\n";
echo "form open: " . substr_count($c, '<form') . "\n";
echo "form close: " . substr_count($c, '</form') . "\n";
