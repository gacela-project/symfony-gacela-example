<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

abstract class AbstractTransfer
{
    /**
     * @return static
     */
    public static function fromArray(array $array)
    {
        $self = new static();
        foreach ($array as $key => $value) {
            if (property_exists($self, $key)) {
                $self->{$key} = $value;
            }
        }

        return $self;
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

    public function toArray(): array
    {
        $self = new static();

        return get_object_vars($self);
    }
}