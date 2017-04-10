<?php

namespace AppBundle\Entity;

trait EntityResolverTrait
{
    /**
     *
     * Simple entity resolver method that looks for setter methods inside
     * our entity for every key in $data array.
     *
     * @author Luka Dolancic <lukadolancic@gmail.com>
     *
     * @param array $data
     * @return object
     */
    public static function resolveFromArray(array $data)
    {
        $class = self::class;

        $entity = new $class();

        foreach ($data as $key => $value) {

            // checks if there is a setter method for an attribute
            $method = 'set' . ucfirst($key);

            // if there is a setter, execute it with a given value
            if (method_exists($entity, $method)) {
                $entity->$method($value);
            }
        }

        return $entity;
    }
}
