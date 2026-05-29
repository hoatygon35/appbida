<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Bida Win</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            @if(preg_match('/Mobile|Android|iP(hone|od|ad)|Windows Phone/i', request()->userAgent()))
                /* Mobile: Thermal Printer K80 */
                @page { margin: 0; size: 80mm auto; } 
                body { 
                    background: #fff; 
                    color: #000; 
                    font-size: 10pt; 
                    width: 80mm;
                    margin: 0 auto;
                }
            @else
                /* PC: Canon 2900 Half A4 (A5 Portrait) */
                @page { margin: 10mm; size: A5 portrait; } 
                body { 
                    background: #fff; 
                    color: #000; 
                    font-size: 11pt; 
                    width: 128mm; /* 148mm A5 width - 20mm total margin */
                    margin: 0 auto;
                }
            @endif
            .no-print { display: none !important; }
            .print-container { 
                width: 100% !important; 
                max-width: 100% !important;
                border: none !important; 
                box-shadow: none !important; 
                padding: 2mm !important; 
                margin: 0 !important;
            }
            /* Ghi đè màu chữ và nền của tailwind để in đen trắng rõ nét */
            * { color: #000 !important; }
        }
    </style>
</head>
<body class="bg-gray-100 py-8">
    <div class="print-container bg-white max-w-sm mx-auto p-6 border border-dashed border-gray-600 shadow-md">
        @yield('content')
    </div>
</body>
</html>
