<?php

/**
 * A rather shabby looking dependency injection container that does
 * lazy instantiation and relies on being crammed into an already-existing registry.
 */
class GhettoDIC
{
    private $dependencies;

    public function set($identifier, Closure $provider)
    {
        $this->dependencies[$identifier] = $provider;
    }

    public function get($identifier)
    {
        if (!isset($this->dependencies[$identifier])) {
            throw new Exception("Dependency {$identifier} not registered.");
        }

        if (is_object($this->dependencies[$identifier]) && $this->dependencies[$identifier] instanceof Closure) {
            $this->dependencies[$identifier] = $this->dependencies[$identifier]();
        }

        return $this->dependencies[$identifier];
    }
}
