<?php
/**
 * Created by JetBrains PhpStorm.
 * User: KESSILER
 * Date: 07/04/13
 * Time: 01:01
 * To change this template use File | Settings | File Templates.
 */
namespace StoredLibrary;

class Classloader
{
    private $namespaces = array();

    public function __construct($path = '')
    {
        $dir = dirname(__DIR__).$path;
        $this->registerNamespace('StoredLibrary', $dir);
    }


    public function getNamespaces()
    {
        return $this->namespaces;
    }

    public function registerNamespace($namespace, $paths)
    {
        $this->namespaces[$namespace] = (array) $paths;
    }

    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }


    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            require $file;
        }
    }

    public function findFile($class)
    {
        if ('\\' == $class[0]) {
            $class = substr($class, 1);
        }

        if (false !== $pos = strrpos($class, '\\')) {
            $namespace = substr($class, 0, $pos);
            foreach ($this->namespaces as $ns => $dirs) {
                foreach ($dirs as $dir) {
                    if (0 === strpos($namespace, $ns)) {
                        $className = substr($class, $pos + 1);
                        $file = $dir.DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';
                        if (file_exists($file)) {
                            return $file;
                        } else {
                            $namespace = str_replace($ns, '', $namespace);
                            $file = $dir.$namespace.DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';
                            if (file_exists($file)) {
                                return $file;
                            }
                        }

                    }
                }
            }
        }
    }


}