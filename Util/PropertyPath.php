<?php

namespace CodeMeme\RulesBundle\Util;

use Symfony\Component\Form\Exception\InvalidPropertyPathException;
use Symfony\Component\Form\Exception\InvalidPropertyException;
use Symfony\Component\Form\Exception\PropertyAccessDeniedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Symfony\Component\Form\Util\PropertyPath as BasePropertyPath;

class PropertyPath extends BasePropertyPath
{

    /**
     * Reads the value of the property at the given index in the path
     *
     * @param  object $object         The object to read from
     * @param  integer $currentIndex  The index of the read property in the path
     * @return mixed                  The value of the property
     */
    protected function readProperty($object, $currentIndex)
    {
        $property = $this->elements[$currentIndex];
    
        if ($this->isIndex[$currentIndex]) {
            if (!$object instanceof \ArrayAccess) {
                throw new InvalidPropertyException(sprintf('Index "%s" cannot be read from object of type "%s" because it doesn\'t implement \ArrayAccess', $property, get_class($object)));
            }
    
            return $object[$property];
        } else {
            $camelProp = $this->camelize($property);
            $reflClass = new \ReflectionClass($object);
            $getter = 'get'.$camelProp;
            $isser = 'is'.$camelProp;
    
            if ($reflClass->hasMethod($property)) {
                return $object->$property();
            } else if ($reflClass->hasMethod($getter)) {
                if (!$reflClass->getMethod($getter)->isPublic()) {
                    throw new PropertyAccessDeniedException(sprintf('Method "%s()" is not public in class "%s"', $getter, $reflClass->getName()));
                }
    
                return $object->$getter();
            } else if ($reflClass->hasMethod($isser)) {
                if (!$reflClass->getMethod($isser)->isPublic()) {
                    throw new PropertyAccessDeniedException(sprintf('Method "%s()" is not public in class "%s"', $isser, $reflClass->getName()));
                }
    
                return $object->$isser();
            } else if ($reflClass->hasMethod('get')) {
                // support `get` accessor method
                return $object->get($property);
            } else if ($reflClass->hasMethod('__get')) {
                // needed to support magic method __get
                return $object->$property;
            } else if ($reflClass->hasProperty($property)) {
                if (!$reflClass->getProperty($property)->isPublic()) {
                    throw new PropertyAccessDeniedException(sprintf('Property "%s" is not public in class "%s". Maybe you should create the method "get%s()" or "is%s()"?', $property, $reflClass->getName(), ucfirst($property), ucfirst($property)));
                }
    
                return $object->$property;
            } else if (property_exists($object, $property)) {
                // needed to support \stdClass instances
                return $object->$property;
            } else {
                throw new InvalidPropertyException(sprintf('Neither property "%s" nor method "%s()" nor method "%s()" exists in class "%s"', $property, $getter, $isser, $reflClass->getName()));
            }
        }
    }
    
    /**
     * Sets the value of the property at the given index in the path
     *
     * @param object  $objectOrArray The object or array to traverse
     * @param integer $currentIndex  The index of the modified property in the
     *                               path
     * @param mixed $value           The value to set
     */
    protected function writeProperty(&$objectOrArray, $currentIndex, $value)
    {
        $property = $this->elements[$currentIndex];
    
        if (is_object($objectOrArray) && $this->isIndex[$currentIndex]) {
            if (!$objectOrArray instanceof \ArrayAccess) {
                throw new InvalidPropertyException(sprintf('Index "%s" cannot be modified in object of type "%s" because it doesn\'t implement \ArrayAccess', $property, get_class($objectOrArray)));
            }
    
            $objectOrArray[$property] = $value;
        } else if (is_object($objectOrArray)) {
            $reflClass = new \ReflectionClass($objectOrArray);
            $setter = 'set'.$this->camelize($property);
    
            if ($reflClass->hasMethod($property)) {
                $objectOrArray->$property($value);
            } else if ($reflClass->hasMethod($setter)) {
                if (!$reflClass->getMethod($setter)->isPublic()) {
                    throw new PropertyAccessDeniedException(sprintf('Method "%s()" is not public in class "%s"', $setter, $reflClass->getName()));
                }
    
                $objectOrArray->$setter($value);
            } else if ($reflClass->hasMethod('set')) {
                // support `set` setter method
                return $objectOrArray->set($property, $value);
            } else if ($reflClass->hasMethod('__set')) {
                // needed to support magic method __set
                $objectOrArray->$property = $value;
            } else if ($reflClass->hasProperty($property)) {
                if (!$reflClass->getProperty($property)->isPublic()) {
                    throw new PropertyAccessDeniedException(sprintf('Property "%s" is not public in class "%s". Maybe you should create the method "set%s()"?', $property, $reflClass->getName(), ucfirst($property)));
                }
    
                $objectOrArray->$property = $value;
            } else if (property_exists($objectOrArray, $property)) {
                // needed to support \stdClass instances
                $objectOrArray->$property = $value;
            } else {
                throw new InvalidPropertyException(sprintf('Neither element "%s" nor method "%s()" exists in class "%s"', $property, $setter, $reflClass->getName()));
            }
        } else {
            $objectOrArray[$property] = $value;
        }
    }
    
}

