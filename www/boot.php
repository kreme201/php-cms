<?php

$parsed_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (0 === strpos($parsed_url, '/assets') && file_exists(__DIR__ . $parsed_url)) {
    // 파일 정보 가져오기
    $file_path = __DIR__ . $parsed_url;
    $file_size = filesize($file_path);
    $file      = fopen($file_path, 'rb');

    // 헤더 설정
    header("Content-Type: " . mime_content_type($file_path));
    header("Content-Length: " . $file_size);
    header('Accept-Ranges: bytes');

    $start = 0;
    $end   = $file_size - 1;

    if (isset($_SERVER['HTTP_RANGE'])) {
        // 범위 요청 처리
        $range = $_SERVER['HTTP_RANGE'];
        list($unit, $range) = explode('=', $range, 2);
        if ($unit == 'bytes') {
            list($range, $extra_ranges) = explode(',', $range, 2);
            list($start, $end) = explode('-', $range);
            $start = intval($start);
            if ($end != '') {
                $end = intval($end);
            } else {
                $end = $file_size - 1;
            }
            header("HTTP/1.1 206 Partial Content");
            header("Content-Range: bytes $start-$end/$file_size");
            header("Content-Length: " . ($end - $start + 1));
        }
    }

    // 파일 스트리밍
    fseek($file, $start);
    $buffer_size = 1024 * 8;
    while (!feof($file) && ($pos = ftell($file)) <= $end) {
        if ($pos + $buffer_size > $end) {
            $buffer_size = $end - $pos + 1;
        }
        set_time_limit(0);
        echo fread($file, $buffer_size);
        flush();
    }

    fclose($file);
    exit;
}

echo '<pre>';
print_r([
    'parsed_url' => $parsed_url,
]);
echo '</pre>';
