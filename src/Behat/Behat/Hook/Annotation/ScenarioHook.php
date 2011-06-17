<?php

namespace Behat\Behat\Hook\Annotation;

use Behat\Behat\Event\EventInterface,
    Behat\Behat\Event\ScenarioEvent;

use Behat\Gherkin\Filter\TagFilter,
    Behat\Gherkin\Filter\NameFilter;

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ScenarioHook hook class.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class ScenarioHook extends FilterableHook
{
    /**
     * {@inheritdoc}
     */
    public function filterMatches(EventInterface $event)
    {
        if (null === ($filterString = $this->getFilter())) {
            return true;
        }

        if ($event instanceof ScenarioEvent) {
            $scenario = $event->getScenario();
        } else {
            $scenario = $event->getOutline();
        }

        if (false !== strpos($filterString, '@')) {
            $filter = new TagFilter($filterString);

            if ($filter->isScenarioMatch($scenario)) {
                return true;
            }
        } elseif (!empty($filterString)) {
            $filter = new NameFilter($filterString);

            if ($filter->isScenarioMatch($scenario)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @see     Behat\Behat\Hook\HookInterface::run()
     */
    public function run(EventInterface $event)
    {
        $context  = $event->getContext();
        $callback = $this->getCallback();

        if (!$this->isClosure()) {
            call_user_func(array($context->getContextByClassName($callback[0]), $callback[1]), $event);
        } else {
            call_user_func($callback, $event);
        }
    }
}
