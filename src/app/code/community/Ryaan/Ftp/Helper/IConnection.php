<?php

interface Ryaan_Ftp_Helper_IConnection
{
    /**
     * @return
     */
    public function downloadFile($remotePath, $localPath);
}
