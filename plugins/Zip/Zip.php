<?php

namespace library\plugins\Zip;

class Zip
{

    //允许后缀名
    private static $allowExt = [
        'jpg', 'png', 'gif',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'txt', 'pdf', 'zip', 'rar',
    ];

    /**
     * 检查是否允许后缀名
     * @param $fileName
     * @return bool
     */
    private function isAllowExt($fileName)
    {
        $ext = array_pop(explode('.', $fileName));
        $ext = trim($ext);
        $ext = strtolower($ext);
        return in_array($ext, self::$allowExt);
    }

    /**
     * 递归找出目录文件列表
     * @param $dir
     * @return array
     */
    private function getDirList($dir)
    {
        $result = array();
        if (is_dir($dir)) {
            $file_dir = scandir($dir);
            foreach ($file_dir as $file) {
                if ($file == '.' || $file == '..' || !$this->isAllowExt($file)) {
                    continue;
                } elseif (is_dir($dir . $file)) {
                    $result = array_merge($result, $this->getDirList($dir . $file . DIRECTORY_SEPARATOR));
                } else {
                    array_push($result, $dir . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        $result = array_unique($result);
        return $result;
    }

    /**
     * @param $dir
     * @param string $zipName
     * @param $save_path
     * @param bool $autoDownload
     * @return bool
     * @throws \Exception
     */
    public function zip($dir, $zipName = 'json', $save_path = null, $autoDownload = true)
    {
        if (!is_dir($dir)) {
            throw new \Exception('目录错误');
        }
        $fileList = $this->getDirList($dir);
        if (!$fileList) {
            return true;
        }
        $save_filename = $zipName . '.zip';
        $save_url = 'zip/' . $save_path ?: '';
        $save_path = realpath(__DIR__ . '/../../') . $save_url;//保存路径（需要真实地址）
        //检测保存目录是否存在，没有就建一个
        if (!file_exists($save_path)) {
            mkdir($save_path);
            @chmod($save_path, 0777);
        }
        $url = $save_url . $save_filename;
        $uri = $save_path . $save_filename;
        if (!file_exists($uri)) {
            //重新生成文件
            $zip = new \ZipArchive();
            if ($zip->open($uri, \ZIPARCHIVE::CREATE) !== true) {
                throw new \Exception('无法打开文件或zip文件创建失败');
            }
            foreach ($fileList as $val) {
                if (file_exists($val)) {
                    //第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
                    $zip->addFile($val, basename($val));
                }
            }
            $zip->close();//关闭
            @chmod($uri, 0777);
        }
        //即使创建，仍有可能失败
        if (!file_exists($uri)) {
            throw new \Exception("压缩文件失败");
        }

        if (true == $autoDownload) {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-disposition: attachment; filename=' . basename($uri)); //文件名
            header("Content-Type: application/zip"); //zip格式的
            header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
            header('Content-Length: ' . filesize($uri)); //告诉浏览器，文件大小
            @readfile($uri);
        } else {
            return $url;
        }
    }

}
