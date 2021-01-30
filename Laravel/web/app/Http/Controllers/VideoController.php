<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    // ls -halt -I. -I.. --full-time | tail -n +2 | sed -n '5,10p'
    // -rw-rw-r-- 1 yifeng yifeng 1.5M 2020-03-29 16:32:04.372477235 +1100 bird4.avi
    // -rw-rw-r-- 1 yifeng yifeng 1.5M 2020-03-29 16:32:01.652530613 +1100 bird3.avi
    // -rw-rw-r-- 1 yifeng yifeng 1.5M 2020-03-29 16:31:58.456596282 +1100 bird2.avi
    // -rw-rw-r-- 1 yifeng yifeng 1.5M 2020-03-29 16:31:55.900651088 +1100 bird1.avi
    // -rw-rw-r-- 1 yifeng yifeng 1.5M 1999-08-20 05:21:14.000000000 +1000 bird.avi
    
    // mediainfo --Inform="Video;%Duration/String%\n" bird4.avi bird3.avi bird3.avi
    // 11 s 833 ms
    // 11 s 833 ms
    // 11 s 833 ms
    //     
    public function index() {
        $current_page = 1;
        list($no_of_files, $files_arr) = $this->getFilesArr($current_page);

        $start_entry = $no_of_files > 0 ? 1 : 0;
        $end_entry = $no_of_files > 7 ? 8 : $no_of_files;
        $previous_page = null;
        $next_page = $no_of_files > 8 ? 2 : null;
        $total_pages = floor($no_of_files / 8) + 1;
        $total_pages = ($total_pages > 1 && ($no_of_files % 8 ==0 )) ? ($total_pages - 1) : $total_pages;

        return view('video.index', [
            'files_arr'     => $files_arr, 
            'no_of_files'   => $no_of_files, 
            'start_entry'   => $start_entry,
            'end_entry'     => $end_entry,
            'current_page'  => $current_page,
            'previous_page' => $previous_page,
            'next_page'     => $next_page,
            'total_pages'   => $total_pages
        ]);
    }

    public function filter()
    {        
        $page                           = (int)request()->get('page', 1);
        list($no_of_files, $files_arr)  = $this->getFilesArr($page);

        $start_entry = $no_of_files > 0 ? ( (($page - 1) * 8) + 1 ) : 0;
        $end_entry = $page * 8 > $no_of_files ? $no_of_files : $page * 8;
        $previous_page = $page > 1 ? ($page -1) : null;
        $total_pages = floor($no_of_files / 8) + 1;
        $total_pages = ($total_pages > 1 && ($no_of_files % 8 ==0 )) ? ($total_pages - 1) : $total_pages;
        $next_page = ( $page == $total_pages) ? null : $page + 1;

        return json_encode([
            'files_arr'     => $files_arr, 
            'no_of_files'   => $no_of_files, 
            'start_entry'   => $start_entry,
            'end_entry'     => $end_entry,
            'current_page'  => $page,
            'previous_page' => $previous_page,
            'next_page'     => $next_page,
            'total_pages'   => $total_pages
        ]);
    }

    public function token()
    {
        $host = request()->getHost();
        $ws_server = "wss://" . $host . "/wss/";        
        $token = request()->get('token');
        $file_name = request()->get('file_name');
        $command = "sudo /bin/bash /scripts/start_downloading.bash " . $token . " " . $file_name . " " . $ws_server . " &>> /log/webrtc-sendrecv.txt";
        shell_exec($command);
    }

    public function getFilesArr($page)
    {
        $no_of_files        = (int)trim(shell_exec('find /videos -type f | wc -l'));

        $start_line         = ( ($page - 1) * 8 ) + 1;

        $end_line           = $start_line + 8;

        $all_files_str      = shell_exec("ls -halt -I. -I.. --full-time /videos | tail -n +2 | sed -n '".$start_line.",".$end_line."p'");

        $all_files_line_arr = explode("\n", $all_files_str);

        $data_arr           = array_map(function($line){
            $line_arr = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
            return ['filename'=>$line_arr[8] ?? '', 'size'=>$line_arr[4] ?? '', 'date'=>$line_arr[5] ?? '', 'time'=>substr($line_arr[6] ?? '', 0, 8)];
        },$all_files_line_arr);

        $data_arr           = array_filter($data_arr, function($each){
            return !empty($each['filename']) && !empty($each['size']) && !empty($each['date']) && !empty($each['time']);
        });

        $file_names_str     = array_reduce($data_arr, function($carry, $data){
            return $carry . ' ' . '/videos/' . $data['filename'];
        }, '');
        
        $byte_size_arr      = explode("\n", trim(shell_exec('ls -alt' . $file_names_str)));

        $files_arr          = array_map(function($data, $byte_size_line) use($data_arr) {
            $byte_size_line_arr = preg_split('/\s+/', $byte_size_line, -1, PREG_SPLIT_NO_EMPTY);
            $file = $data;
            $file['start_time'] = strpos($file['filename'], '-') !== false ? str_replace('-', ':', substr($file['filename'], 11, 8)) : date('H:i:s', substr($file['filename'], 0, -10));
            $file['byte_size'] = $byte_size_line_arr[4] ?? 0;
            if (strpos($file['filename'], '-') !== false) {
                $filename_index = substr($file['filename'], 20, -4);
                if ($filename_index != '00') {
                    $prev_filename_index = intval($filename_index) - 1;
                    $prev_filename_index_str = sprintf("%02d", $prev_filename_index);
                    $prev_filename = substr($file['filename'], 0, 20) . $prev_filename_index_str . '.avi';
                    $prev_file_data = current(array_filter($data_arr, function($e) use($prev_filename) { 
                        return $e['filename'] == $prev_filename;
                    }));
                    if ($prev_file_data) {
                        $file['start_time'] = $prev_file_data['time'];
                    }
                }
            }
            return $file;
        }, $data_arr, $byte_size_arr);  

        return [$no_of_files, $files_arr];
    }
}
