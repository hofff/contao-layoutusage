<?php

declare(strict_types=1);

namespace Hofff\Contao\LayoutUsage\EventListener;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\MenuEvent;
use Hofff\Contao\LayoutUsage\Controller\LayoutUsageController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsEventListener(ContaoCoreEvents::BACKEND_MENU_BUILD, priority: -255)]
final class BackendMenuListener
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function __invoke(MenuEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $tree    = $event->getTree();

        if ($request === null || $tree->getName() !== 'mainMenu') {
            return;
        }

        if ($request->attributes->get('_route') !== LayoutUsageController::class) {
            return;
        }

        $tree->getChild('design')?->getChild('themes')?->setCurrent(true);
    }
}
