<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCi_Environment_UtilHelper extends KmsCi_Environment_BaseHelper {

    /**
     * @param string $filename
     * @return bool if file exists - ensure to unlink, if it doesn't exist return true
     */
    public function softUnlink($filename)
    {
        if (file_exists($filename) && !unlink($filename)) {
            return $this->error('failed to unlink '.$filename);
        }
        return true;
    }

    public function softRename($old, $new)
    {
        if (file_exists($old) && !rename($old, $new)) {
            return $this->error('failed to rename '.$old.' to '.$new);
        }
        return true;
    }

    // recursively remove a directory
    public function rrmdir($dir) {
        foreach(glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                if (!$this->rrmdir($file)) {
                    return $this->error('failed to recursively remove directory '.$file);
                }
            } elseif (!$this->softUnlink($file)) {
                return false;
            }
        }
        if (!rmdir($dir)) {
            return $this->error('failed to rmdir '.$dir);
        } else {
            return true;
        }
    }

    public function softCopy($src, $dest)
    {
        if (!file_exists($src)) {
            return true;
        } else {
            return copy($src, $dest) ? true : $this->error('Failed to copy from '.$src.' to '.$dest);
        }
    }

    public function mkdir($path)
    {
        return mkdir($path) ? true : $this->error('failed to create directory '.$path);
    }

    public function softMkdir($path)
    {
        if (file_exists($path)) {
            return true;
        } else {
            return mkdir($path, 0777, true);
        }
    }

    public function normalizePath($path)
    {
        return str_replace('\\', '/', $path);
    }

}
