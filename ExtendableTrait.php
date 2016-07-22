<?php

/**
 * Extension trait
 * Allows for "Private traits"
 *
 * @package october\extension
 * @author Alexey Bobkov, Samuel Georges
 */

trait ExtendableTrait
{
    /**
     * @var array Class reflection information, including behaviors.
     */
    protected $extensionData = [
        'extensions'     => [],
        'methods'        => []
    ];

    /**
     * @var array Used to extend the constructor of an extendable class.
     * Eg: Class::extend(function($obj) { })
     */
    protected static $extendableCallbacks = [];

    /**
     * @var array Collection of static methods used by behaviors.
     */
    protected static $extendableStaticMethods = [];

    /**
     * @var bool Indicates if dynamic properties can be created.
     */
    protected static $extendableGuardProperties = true;

    /**
     * Constructor.
     */
    public function extendableConstruct()
    {
        /*
         * Apply extensions
         */
        if (!$this->implement) {
            return;
        }

        if (is_string($this->implement)) {
            $uses = explode(',', $this->implement);
        }
        elseif (is_array($this->implement)) {
            $uses = $this->implement;
        }
        else {
            throw new Exception(sprintf('Class %s contains an invalid $implement value', get_class($this)));
        }

        foreach ($uses as $use) {
            $useClass = str_replace('.', '\\', trim($use));

            /*
             * Soft implement
             */
            if (substr($useClass, 0, 1) == '@') {
                $useClass = substr($useClass, 1);
                if (!class_exists($useClass)) continue;
            }

            $this->extendClassWith($useClass);
        }
    }

    /**
     * Extracts the available methods from a behavior and adds it to the
     * list of callable methods.
     * @param  string $extensionName
     * @param  object $extensionObject
     * @return void
     */
    protected function extensionExtractMethods($extensionName, $extensionObject)
    {
        $extensionMethods = get_class_methods($extensionName);
        foreach ($extensionMethods as $methodName) {
            if (
                $methodName == '__construct'
            ) {
                continue;
            }

            $this->extensionData['methods'][$methodName] = $extensionName;
        }
    }

    /**
     * Dynamically extend a class with a specified behavior
     * @param  string $extensionName
     * @return void
     */
    public function extendClassWith($extensionName)
    {
        if (!strlen($extensionName)) {
            return $this;
        }

        if (isset($this->extensionData['extensions'][$extensionName])) {
            throw new Exception(sprintf(
                'Class %s has already been extended with %s',
                get_class($this),
                $extensionName
            ));
        }

        $this->extensionData['extensions'][$extensionName] = $extensionObject = new $extensionName($this);
        $this->extensionExtractMethods($extensionName, $extensionObject);
    }

    /**
     * Check if extendable class is extended with a behavior object
     * @param  string $name Fully qualified behavior name
     * @return boolean
     */
    public function isClassExtendedWith($name)
    {
        $name = str_replace('.', '\\', trim($name));
        return isset($this->extensionData['extensions'][$name]);
    }

    /**
     * Returns a behavior object from an extendable class, example:
     *
     *   $this->getClassExtension('Backend.Behaviors.FormController')
     *
     * @param  string $name Fully qualified behavior name
     * @return mixed
     */
    public function getClassExtension($name)
    {
        $name = str_replace('.', '\\', trim($name));
        return (isset($this->extensionData['extensions'][$name]))
            ? $this->extensionData['extensions'][$name]
            : null;
    }

    /**
     * Short hand for getClassExtension() method, except takes the short
     * extension name, example:
     *
     *   $this->asExtension('FormController')
     *
     * @param  string $shortName
     * @return mixed
     */
    public function asExtension($shortName)
    {
        $hints = [];
        foreach ($this->extensionData['extensions'] as $class => $obj) {
            if (
                preg_match('@\\\\([\w]+)$@', $class, $matches) &&
                $matches[1] == $shortName
            ) {
                return $obj;
            }
        }
    }

    /**
     * Checks if a method exists, extension equivalent of method_exists()
     * @param  string $name
     * @return boolean
     */
    public function methodExists($name)
    {
        return (
            method_exists($this, $name) ||
            isset($this->extensionData['methods'][$name])
        );
    }

    /**
     * Checks if a property is accessible, property equivalent of is_callabe()
     * @param  mixed  $class
     * @param  string $propertyName
     * @return boolean
     */
    protected function extendableIsAccessible($class, $propertyName)
    {
        $reflector = new ReflectionClass($class);
        $property = $reflector->getProperty($propertyName);
        return $property->isPublic();
    }

    /**
     * Magic method for __call()
     * @param  string $name
     * @param  array  $params
     * @return mixed
     */
    public function extendableCall($name, $params = null)
    {
        if (isset($this->extensionData['methods'][$name])) {
            $extension = $this->extensionData['methods'][$name];
            $extensionObject = $this->extensionData['extensions'][$extension];

            if (method_exists($extension, $name) && is_callable([$extension, $name])) {
                return call_user_func_array(array($extensionObject, $name), $params);
            }
        }

        $parent = get_parent_class();
        if ($parent !== false && method_exists($parent, '__call')) {
            return parent::__call($name, $params);
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            get_class($this),
            $name
        ));
    }

    /**
     * Magic method for __callStatic()
     * @param  string $name
     * @param  array  $params
     * @return mixed
     */
    public static function extendableCallStatic($name, $params = null)
    {
        $className = get_called_class();

        if (!array_key_exists($className, self::$extendableStaticMethods)) {

            self::$extendableStaticMethods[$className] = [];

            $class = new ReflectionClass($className);
            $defaultProperties = $class->getDefaultProperties();
            if (array_key_exists('implement', $defaultProperties)) {
                $implement = $defaultProperties['implement'];

                if (is_string($implement)) {
                    $uses = explode(',', $implement);
                }
                elseif (is_array($implement)) {
                    $uses = $implement;
                }
                else {
                    throw new Exception(sprintf('Class %s contains an invalid $implement value', $className));
                }

                foreach ($uses as $use) {
                    $useClassName = str_replace('.', '\\', trim($use));

                    $useClass = new ReflectionClass($useClassName);
                    $staticMethods = $useClass->getMethods(ReflectionMethod::IS_STATIC);
                    foreach ($staticMethods as $method) {
                        self::$extendableStaticMethods[$className][$method->getName()] = $useClassName;
                    }
                }
            }

        }

        if (isset(self::$extendableStaticMethods[$className][$name])) {
            $extension = self::$extendableStaticMethods[$className][$name];

            if (method_exists($extension, $name) && is_callable([$extension, $name])) {
                $extension::$extendableStaticCalledClass = $className;
                $result = forward_static_call_array(array($extension, $name), $params);
                $extension::$extendableStaticCalledClass = null;
                return $result;
            }
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            $className,
            $name
        ));
    }

}
