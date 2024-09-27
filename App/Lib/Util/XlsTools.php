<?php


/*这个是类包*/

class xlsTools
{

    var $inEncode = 'utf-8';
    var $outEncode = 'gb2312';

    protected $rowCount; //存储已经存在内存中的记录条数


    protected $rowFlushCount; //  一次flush的数据条数

    public function __construct($rowFlushCount = 1000)
    {
        $this->rowFlushCount = $rowFlushCount;
        $this->rowCount = 0;
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function start($param)
    {
        // todo 文件名这里建议允许自定 ddcoder
        $filename = $param['type'] . '-' . date('YmdHis') . '(' . $param['name'] . ')';
        $this->doStart($param['title'], $filename);
    }

    public function doStart($keys, $filename)
    {
        $this->download($filename . '.xls');

        // php输出到缓冲区
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><style>td{vnd.ms-excel.numberformat:@}</style></head>';
        echo '<table width="100%" border="1">';
        // 有时候需要自定义表头 ddcdoer 修改于2017-08-26
        if ($keys) {
            echo '<tr><th filter=all>' . implode('</th><th filter=all>', $keys) . "</th></tr>\r\n";
        }

        //刷新缓冲区
        ob_flush();
        flush();
    }

    //下载文件
    //$mimeType = 'application/force-download'
    //Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
    //Content-Type: vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
    //$mimeType = 'application/vnd.ms-excel'
    function download($fname = 'data', $data = null, $mimeType = 'application/force-download')
    {
        if (headers_sent($file, $line)) {
            echo 'Header already sent @ ' . $file . ':' . $line;
            exit();
        }
        //header('Cache-Control: no-cache;must-revalidate');
        // //fix ie download bug
        header('Pragma: no-cache, no-store');
        header("Expires: Wed, 26 Feb 1997 08:21:57 GMT");
        if (strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE')) {
            $fname = urlencode($fname);
            header('Content-type: ' . $mimeType);
        } else {
            header('Content-type: ' . $mimeType . ';charset=utf-8');
        }
        header("Content-Disposition: attachment; filename=\"" . $fname . '"');
        //header( "Content-Description: File Transfer");
        if ($data) {
            header('Content-Length: ' . strlen($data));
            echo $data;
            exit();
        }
    }

    public function csv_export($keys, $expData, $type, $count)
    {
        $csv = '';
        foreach ($keys as $key) {
            $csv .= '"' . iconv("UTF-8", "gb2312", $key) . '",';
        }
        $csv .= "\n";

        foreach ($expData as $expArr) {
            foreach ($expArr as $e_key => $exp) {
                $csv .= iconv("UTF-8", "gbk", $this->escapeCSV($expArr[$e_key])) . ',';
            }
            $csv .= "\n";
        }
        ob_end_clean();
        $fileName = $type . '-' . date('YmdHis') . '(' . $count . ').csv';
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . iconv("UTF-8", "gb2312", $fileName));
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        ob_end_clean();
        echo $csv;
    }

    /**
     * 生成并下载csv文件
     */
    public function csv_export_f($keys, $expData, $fileName)
    {
        // export file
        $csv = '';
        foreach ($keys as $key) {
            $csv .= '"' . iconv("UTF-8", "gb2312", $key) . '",';
        }
        $csv .= "\n";

        foreach ($expData as $expArr) {
            foreach ($expArr as $e_key => $exp) {
                $csv .= iconv("UTF-8", "gbk", $this->escapeCSV($expArr[$e_key])) . ',';
            }
            $csv .= "\n";
        }
        ob_end_clean();
        $fileName .= '.csv';
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . iconv("UTF-8", "gb2312", $fileName));
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        ob_end_clean();
        echo $csv;
    }

    function escapeCSV($str)
    {
        $str = str_replace(array(',', '"', "\n\r"), array('', '""', ''), $str);
        if ($str == "") {
            $str = '""';
        }
        return $str;
    }

    public function allData($rows)
    {
        foreach ($rows as $row) {
            echo '<tr><td>' . implode('</td><td>', $row) . "</td></tr>\r\n";
        }
        ob_flush();
        flush();
    }

    public function oneData($row)
    {
        echo '<tr><td>' . implode('</td><td>', $row) . "</td></tr>\r\n";
        ob_flush();
        flush();
    }

    function end()
    {
        echo '</table>';
        ob_flush();
        flush();
    }

    /*
     * 多条数据flush一次 默认1000，有初始化对象决定
     * */
    public function multiData($row)
    {
        $this->rowCount++;
        echo '<tr><td>' . implode('</td><td>', $row) . "</td></tr>\r\n";
        if ($this->rowCount >= $this->rowFlushCount) {
            ob_flush();
            flush();
        }

    }
}
