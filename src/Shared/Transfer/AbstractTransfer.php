<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

use RuntimeException;

use function get_object_vars;
use function lcfirst;
use function preg_replace;
use function property_exists;
use function reset;

abstract class AbstractTransfer
{
    /**
     * @param array<string,mixed> $array
     *
     * @return static
     *
     * @psalm-suppress MixedAssignment
     */
    public function fromArray(array $array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param list<mixed> $arguments
     *
     * @return mixed|static
     */
    public function __call(string $name, array $arguments = [])
    {
        $withoutPrefix = (string)preg_replace('/^get/', '', $name);
        $normalizedName = lcfirst($withoutPrefix);
        if (property_exists($this, $normalizedName)) {
            return $this->{$normalizedName};
        }

        $withoutPrefix = (string)preg_replace('/^set/', '', $name);
        $normalizedName = lcfirst($withoutPrefix);
        if (property_exists($this, $normalizedName)) {
            $this->{$normalizedName} = reset($arguments);
            return $this;
        }

        throw new RuntimeException("Unknown property with name: $normalizedName");
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->__call($name);
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->__call($name, [$value]);
    }

    public function __isset(string $name): bool
    {
        return $this->__call($name) !== null;
    }
}
