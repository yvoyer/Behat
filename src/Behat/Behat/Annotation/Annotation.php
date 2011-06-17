<?php

namespace Behat\Behat\Annotation;

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Behat annotation class.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class Annotation implements AnnotationInterface
{
    /**
     * Is callback a closure.
     *
     * @var     Boolean
     */
    private $closure;
    /**
     * Annotation callback.
     *
     * @var     callback
     */
    private $callback;
    /**
     * Definition path.
     *
     * @var     string
     */
    private $path;

    /**
     * Constructs annotation.
     *
     * @param   callback    $callback
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Callback should be a valid callable');
        }
        $this->closure = !is_array($callback);

        if (!$this->isClosure()) {
            $this->path = $callback[0] . '::' . $callback[1] . '()';
        } else {
            $reflection = new \ReflectionFunction($callback);
            $this->path = $reflection->getFileName() . ':' . $reflection->getStartLine();
        }

        $this->callback = $callback;
    }

    /**
     * @see Behat\Behat\Annotation\AnnotationInterface::getPath()
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @see Behat\Behat\Annotation\AnnotationInterface::isClosure()
     */
    public function isClosure()
    {
        return $this->closure;
    }

    /**
     * @see Behat\Behat\Annotation\AnnotationInterface::getCallback()
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @see Behat\Behat\Annotation\AnnotationInterface::getCallbackReflection()
     */
    public function getCallbackReflection()
    {
        if (!$this->isClosure()) {
            return new \ReflectionMethod($this->callback[0], $this->callback[1]);
        } else {
            return new \ReflectionFunction($this->callback);
        }
    }
}
