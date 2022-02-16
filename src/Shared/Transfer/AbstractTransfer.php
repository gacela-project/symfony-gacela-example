<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

abstract class AbstractTransfer
{
    public function fromArray(array $array): self
    {
        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function __call($name, $arguments)
    {
        // fluent getters
        $withoutPrefix = (string)preg_replace('/^get/', '', $name);
        $normalizedName = lcfirst($withoutPrefix);
        if (property_exists($this, $normalizedName)) {
            return $this->{$normalizedName};
        }

        // fluent setters
        $withoutPrefix = (string)preg_replace('/^set/', '', $name);
        $normalizedName = lcfirst($withoutPrefix);
        if (property_exists($this, $normalizedName)) {
            $this->{$normalizedName} = reset($arguments);
            return $this;
        }

        return null;
    }
}