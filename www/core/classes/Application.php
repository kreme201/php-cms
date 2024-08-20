<?php declare(strict_types=1);

namespace Kreme;

class Application {
    public function serve(): void {
        $parsed_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (str_starts_with($parsed_url, '/' . trim(STATIC_PREFIX, '/'))) {
            $static_path = APP_ROOT . str_replace(trim(STATIC_PREFIX, '/'), 'statics', $parsed_url);

            if (file_exists($static_path)) {
                $this->serve_file($static_path);
            } else {
                die(http_response_code(404));
            }
        }

        echo '<pre>';
        print_r(compact('parsed_url'));
        echo '</pre>';
    }

    protected function serve_file(string $file_path): never {
        // 파일 정보 가져오기
        $file_size = filesize($file_path);
        $file      = fopen($file_path, 'rb');

        // 헤더 설정
        header('Content-Type: ' . get_mime_type($file_path));
        header('Content-Length: ' . $file_size);
        header('Accept-Ranges: bytes');

        $start = 0;
        $end   = $file_size - 1;

        if (isset($_SERVER['HTTP_RANGE'])) {
            // 범위 요청 처리
            $range = $_SERVER['HTTP_RANGE'];
            [$unit, $range] = explode('=', $range, 2);
            if ($unit == 'bytes') {
                [$range, $extra_ranges] = explode(',', $range, 2);
                [$start, $end] = explode('-', $range);
                $start = intval($start);
                if ($end != '') {
                    $end = intval($end);
                } else {
                    $end = $file_size - 1;
                }
                header('HTTP/1.1 206 Partial Content');
                header('Content-Range: bytes ' . ($start - $end) / $file_size);
                header('Content-Length: ' . ($end - $start + 1));
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
}
