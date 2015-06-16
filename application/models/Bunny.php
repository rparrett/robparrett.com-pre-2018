<?php

class BunnyModel
{
    private $feedError = "";
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function feed($test = false)
    {
        $this->setFeedError(false);

        $accessToken = $this->config['particle']['accessToken'];
        $deviceId = $this->config['particle']['deviceId'];

        $particle = new ParticleAPI($accessToken);
        $result = $particle->call($deviceId, $test ? 'test' : 'go');

        $error = "";

        if (!$result) {
            $error = "No response from Spark API.";
        } elseif (isset($result->error)) {
            $error = "Spark API: $result->error";
        } elseif (!isset($result->return_value)) {
            $error = "Spark API: Response did not contain return value.";
        } elseif (!$result->return_value) {
            $error = "Spark API: Endpoint did not return success.";
        }
        
        $this->setLastFed();

        if ($error) {
            $this->setFeedError($error);
            return false;
        }

        return true;
    }

    private function setFeedError($error)
    {
        $this->feedError = $error;
    }

    public function getFeedError()
    {
        if ($this->feedError) {
            return $this->feedError;
        }

        return "No error.";
    }

    public function getLastFed()
    {
        $timestampFile = $this->config['bunny']['timestampFile'];

        return filemtime($timestampFile);
    }

    private function setLastFed($time = null)
    {
        if (is_null($time)) {
            $time = time();
        }
        
        $timestampFile = $this->config['bunny']['timestampFile'];
        touch($timestampFile);
    }
}
