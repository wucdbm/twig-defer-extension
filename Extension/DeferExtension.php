<?php

namespace Wucdbm\Extension\Twig\Extension;

use Wucdbm\Extension\Twig\TokenParser\DeferredTokenParser;
use Wucdbm\Extension\Twig\TokenParser\DeferTokenParser;

class DeferExtension extends \Twig_Extension {

    const NAME_DEFAULT = '_default';

    protected $cache = [];

    public function defer($key, $value) {
        if (isset($this->cache[$key])) {
            $this->cache[$key] .= $value;
        } else {
            $this->cache[$key] = $value;
        }
    }

    public function flush($key) {
        if (!$this->has($key)) {
            throw new \Exception(sprintf('Nothing is deferred for key %s', $key));
        }

        $data = $this->cache[$key];
        unset($this->cache[$key]);

        return $data;
    }

    public function has($key) {
        return isset($this->cache[$key]);
    }

    public function getTokenParsers() {
        return [
            new DeferTokenParser(),
            new DeferredTokenParser()
        ];
    }

    public function getTests() {
        return [
            new \Twig_SimpleTest('deferred', function ($key) {
                return $this->has($key);
            })
        ];
    }

    public function getName() {
        return 'defer';
    }

}