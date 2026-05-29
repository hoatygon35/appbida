<?php
$content = file_get_contents('f:/CAIDAT/laragon/www/bida/resources/views/admin/dashboard.blade.php');
$lines = explode("\n", $content);
$depth = 0;
$xDataLine = -1;
foreach ($lines as $index => $line) {
    if (strpos($line, 'x-data') !== false && $xDataLine === -1) {
        $xDataLine = $index + 1;
        echo "Found x-data at line $xDataLine\n";
    }
    
    // Simple tag counting per line is rough because of <div ... > vs </div>
    // Let's use preg_match_all
    $openCount = preg_match_all('/<div\b[^>]*>/i', $line, $matches);
    $closeCount = preg_match_all('/<\/div>/i', $line, $matches);
    
    if ($xDataLine !== -1 && $index >= $xDataLine - 1) {
        $depth += $openCount;
        $depth -= $closeCount;
        if ($depth === 0) {
            echo "x-data div closes at line " . ($index + 1) . "\n";
            echo "Line content: " . trim($line) . "\n";
            break;
        }
    }
}
echo "Current depth at end of file: $depth\n";
