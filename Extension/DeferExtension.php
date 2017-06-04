<?php

namespace Wucdbm\Extension\Twig\Extension;

use Wucdbm\Extension\Twig\TokenParser\DeferTokenParser;

class DeferExtension extends \Twig_Extension {

    protected $strict;

    protected $cache = [];

    public function __construct($strict = false) {
        $this->strict = $strict;
    }

    public function defer($key, $value) {
        if (isset($this->cache[$key])) {
            $this->cache[$key] .= $value;
        } else {
            $this->cache[$key] = $value;
        }
    }

    public function flush($key) {
        if (!$this->has($key)) {
            if ($this->strict) {
                throw new \Exception(sprintf('Nothing is deferred for key %s', $key));
            }

            return '';
        }

        $data = $this->cache[$key];

        unset($this->cache[$key]);

        return $data;
    }

    public function has($key) {
        return isset($this->cache[$key]);
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('findDeferred', [$this, 'findDeferred'])
        ];
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('deferred', [$this, 'deferred'])
        ];
    }

    public function findDeferred(string $keyStart = null) {
        if (null === $keyStart) {
            return array_keys($this->cache);
        }

        $found = [];

        foreach ($this->cache as $key => $item) {
            if (0 !== strpos($key, $keyStart)) {
                continue;
            }

            $found[] = $key;
        }

        return $found;
    }

    public function deferred(string $key, $default = null) {
        if (!$this->has($key) && $default) {
            return $default;
        }

        return $this->flush($key);
    }

    public function getTokenParsers() {
        return [
            new DeferTokenParser()
        ];
    }

    public function getTests() {
        return [
            new \Twig_SimpleTest('deferred', function ($key) {
                return $this->has($key);
            })
        ];
    }

}