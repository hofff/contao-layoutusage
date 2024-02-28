<?php

declare(strict_types=1);

namespace Hofff\Contao\LayoutUsage\Controller;

use Contao\Backend;
use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractBackendController;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Image;
use Contao\LayoutModel;
use Contao\Message;
use Contao\System;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_filter;
use function count;
use function implode;

/** @psalm-suppress PropertyNotSetInConstructor */
#[Route(
    '/%contao.backend.route_prefix%/themes/layout-usage/{layoutId}',
    name: self::class,
    requirements: ['layoutId' => '\d+'],
    defaults: ['_scope' => 'backend'],
)]
#[IsGranted(ContaoCorePermissions::USER_CAN_ACCESS_LAYOUTS)]
#[AsController]
final class LayoutUsageController extends AbstractBackendController
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Connection $connection,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function __invoke(int $layoutId): Response
    {
        $this->initializeContaoFramework();

        $layout = $this->getContaoAdapter(LayoutModel::class)->findByPk($layoutId);
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        if (! $layout instanceof LayoutModel) {
            throw new NotFoundHttpException();
        }

        $this->getContaoAdapter(System::class)->loadLanguageFile('tl_page');
        $this->getContaoAdapter(Controller::class)->loadDataContainer('tl_page');

        $usages = $this->getUsages($layoutId);
        $count  = count($usages);

        foreach ($usages as &$usage) {
            $usage['maintenanceMode'] = false;

            $count             += $usage['inherited'];
            $usage['icon']      = $this->getContaoAdapter(Backend::class)
                ->addPageIcon($usage, '', blnReturnImage: true);
            $usage['pageUrl']   = $this->urlGenerator->generate(
                'contao_backend',
                [
                    'do' => 'page',
                    'pn' => $usage['id'],
                    'rt' => $this->container->get('contao.csrf.token_manager')->getDefaultTokenValue(),
                ],
            );
            $usage['editUrl']   = $this->urlGenerator->generate(
                'contao_backend',
                [
                    'do'  => 'page',
                    'act' => 'edit',
                    'id'  => $usage['id'],
                    'rt'  => $this->container->get('contao.csrf.token_manager')->getDefaultTokenValue(),
                ],
            );
            $usage['editTitle'] = $this->translator->trans('tl_page.edit', [$usage['id']], 'contao_tl_page');
            $usage['editIcon']  = Image::getHtml('edit.svg', $usage['editTitle']);
        }

        unset($usage);

        $data = [
            'headline' => $this->compileHeadline($layout),
            'layout'   => $layout,
            'usages'   => $usages,
            'count'    => $count,
            'referer'  => $this->getContaoAdapter(System::class)->getReferer(true),
            'message'  => Message::generate(),
        ];

        return $this->render('@HofffContaoLayoutUsage/backend/layout_usage.html.twig', $data);
    }

    /** @return list<array<string,mixed>> */
    private function getUsages(int $layoutId): array
    {
        $usages = [];
        $sql    = <<<'SQL'
SELECT
	id,
	title,
	type,
	published,
	start,
	stop,
	hide,
	protected
FROM
	tl_page
WHERE
	includeLayout = 1
	AND layout = ?
SQL;

        $result = $this->connection->executeQuery($sql, [$layoutId]);

        while ($page = $result->fetchAssociative()) {
            $page['inherited'] = $this->getInheritedCount($page['id']);
            $usages[]          = $page;
        }

        return $usages;
    }

    private function getInheritedCount(int $pageId): int
    {
        $pids  = [$pageId];
        $count = 0;

        while ($pids) {
            $sql  = 'SELECT id FROM tl_page WHERE includeLayout = \'\' AND pid IN (:pids)';
            $pids = $this->connection
                ->executeQuery($sql, ['pids' => $pids], ['pids' => ArrayParameterType::STRING])
                ->fetchFirstColumn();

            $count += count($pids);
        }

        return $count;
    }

    private function compileHeadline(LayoutModel $layout): string
    {
        $headline = [
            $this->translator->trans('MOD.themes.0', [], 'contao_modules'),
            $layout->getRelated('pid')?->name,
            $this->translator->trans('MOD.tl_layout', [], 'contao_modules'),
            $this->translator->trans(
                'tl_layout.hofff_layoutusage_headline',
                [$layout->name, $layout->id],
                'contao_tl_layout',
            ),
        ];

        return '<span>' . implode('</span><span>', array_filter($headline)) . '</span>';
    }
}
