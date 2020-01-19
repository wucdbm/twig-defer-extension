<?php

namespace Wucdbm\Extension\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;
use Wucdbm\Extension\Twig\TokenParser\DeferTokenParser;

class DeferExtension extends AbstractExtension {

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

    public function flush($key, $default = null) {
        if (!$this->has($key)) {
            return $default;
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
            new TwigFunction('findDeferred', [$this, 'findDeferred'])
        ];
    }

    public function getFilters() {
        return [
            new TwigFilter('deferred', [$this, 'flush'])
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

    public function getTokenParsers() {
        return [
            new DeferTokenParser()
        ];
    }

    public function getTests() {
        return [
            new TwigTest('deferred', function ($key) {
                return $this->has($key);
            })
        ];
    }

}