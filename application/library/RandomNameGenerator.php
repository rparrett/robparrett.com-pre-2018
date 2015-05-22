<?php

/**
 * A Random Name Generator
 */
class RandomNameGenerator
{
    private $patterns;
    private $dicts;

    /**
     * Create a new Random Name Generator
     *
     * @param array $config An array containing configuration data.
     *    ex: array(
     *      'path' => '/var/dict/',
     *      'patterns' => array(
     *          array('adjective.dict', 'noun.dict')
     *      )
     *    )
     */

    public function __construct($config)
    {
        $this->patterns = array_values($config['patterns']);

        $files = glob($config['path'] . '*');

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);

            if (!isset($this->dicts[$filename])) {
                $this->dicts[$filename] = array();
            }

            $contents = file_get_contents($file);
            $words = explode("\n", $contents);

            foreach ($words as $word) {
                if (!$word) {
                    continue;
                }

                $this->dicts[$filename][] = $word;
            }
        }
    }

    public function get($maxLength = null)
    {
        while (true) {
            $pattern = $this->patterns[mt_rand(0, count($this->patterns)-1)];

            $words = array();

            foreach ($pattern as $dict) {
                $words[] = $this->dicts[$dict][mt_rand(0, count($this->dicts[$dict])-1)];
            }

            $name = implode(" ", $words);

            if (!is_null($maxLength) && strlen($name) > $maxLength) {
                continue;
            }

            // a -> an where appropriate

            $name = preg_replace('/^((?:what)? ?a) ([aeiou])/', '$1n $2', $name);

            return $name;
        }
    }
}
