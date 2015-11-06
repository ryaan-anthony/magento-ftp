<?php

class Ryaan_Ftp_Helper_Data
{
    /** @var Ryaan_Ftp_Helper_IConnection */
    protected $connection;

    /**
     * @param array
     */
    public function __construct(array $args = [])
    {
        list($this->connection) = $this->checkTypes(
            $this->nullCoalesce($args, 'connection', Mage::helper('ftp/connection'))
        );
    }

    /**
     * @param Ryaan_Ftp_Helper_IConnection
     * @return array
     */
    protected function checkTypes(Ryaan_Ftp_Helper_IConnection $connection)
    {
        return func_get_args();
    }
    /**
     * Fill in default values.
     *
     * @param  array
     * @param  string
     * @param  mixed
     * @return mixed
     */
    protected function nullCoalesce(array $arr, $key, $default)
    {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    /**
     * @param string
     * @param string
     */
    public function downloadFile($remotePath, $localPath)
    {
        $this->connection->downloadFile($remotePath, $localPath);
    }

}
