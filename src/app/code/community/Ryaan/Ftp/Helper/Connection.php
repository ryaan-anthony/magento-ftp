<?php

class Ryaan_Ftp_Helper_Connection extends Mage_Core_Helper_Abstract implements Ryaan_Ftp_Helper_IConnection
{
    const CONNECTION_FAILED_MESSAGE = 'Ryaan_Ftp_Exception_ConnectionFailed';

    const WRITE_FAILED_MESSAGE = 'Ryaan_Ftp_Exception_WriteFailed';

    const CONNECTION_CONFIG_NODE = 'ftp/connection';

    /** @var Varien_Io_Interface */
    protected $filesystem;

    /** @var Varien_Io_Interface */
    protected $client;

    /** @var Mage_Core_Model_Config */
    protected $config;

    public function __construct(array $args = [])
    {
        list($this->filesystem, $this->client, $this->config) = $this->checkTypes(
            $this->nullCoalesce($args, 'filesystem', new Varien_Io_File()),
            $this->nullCoalesce($args, 'client', new Varien_Io_Ftp()),
            $this->nullCoalesce($args, 'config', Mage::getConfig())
        );
    }

    /**
     * @param Varien_Io_Interface
     * @param Varien_Io_Interface
     * @param Mage_Core_Model_Config
     * @return array
     */
    protected function checkTypes(
        Varien_Io_Interface $filesystem,
        Varien_Io_Interface $client,
        Mage_Core_Model_Config $config
    ) {
        return func_get_args();
    }

    /**
     * @param  array
     * @param  string
     * @param  mixed
     * @return mixed
     */
    protected function nullCoalesce(array $arr, $key, $default)
    {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    public function downloadFile($remotePath, $localPath)
    {
        // open the connection
        $this->openConnection();

        $pathInfo = pathinfo($remotePath);
        $pathToFile = $pathInfo['dirname'];
        $fileName = $pathInfo['basename'];

        $this->client->cd($pathToFile);

        // read the content of a remote file
        $fileContents = $this->client->read($fileName);

        // write the content to the local filesystem
        $result = $this->filesystem->write($localPath, $fileContents);

        // close the connection
        $this->closeConnection();

        if (!$result) {

            $message = $this->__(static::WRITE_FAILED_MESSAGE);

            throw new Ryaan_Ftp_Exception_WriteFailed($message);
        }
    }

    /**
     * Close the connection
     */
    protected function closeConnection()
    {
        $this->client->close();
    }

    /**
     * Open the connection
     * @throws Ryaan_Ftp_Exception_ConnectionFailed
     */
    protected function openConnection()
    {
        $message = null;

        $configData = $this->getConfigData();

        try {

            return $this->client->open([
                'host'      => (string) $configData->hostname,
                'user'      => (string) $configData->username,
                'password'  => (string) $configData->password,
                'timeout'   => (int) $configData->timeout,
            ]);

        } catch (Varien_Io_Exception $e) {

            $message = $e->getMessage();

        }

        throw new Ryaan_Ftp_Exception_ConnectionFailed($message ?: $this->__(static::CONNECTION_FAILED_MESSAGE));
    }

    /**
     * @return string
     */
    protected function getStoreCode()
    {
        return Mage::app()->getStore()->getCode();
    }

    /**
     * @return Mage_Core_Model_Config_Element
     */
    protected function getConfigData()
    {
        return $this->config->getNode(static::CONNECTION_CONFIG_NODE);
    }

}
