<?php

declare(strict_types=1);

namespace Hofff\Contao\LayoutUsage\EventListener\Dca\Layout;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Doctrine\DBAL\Connection;
use Hofff\Contao\LayoutUsage\Controller\LayoutUsageController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

#[AsCallback('tl_layout', 'list.operations.hofff_layoutusage_btn.button')]
final class LayoutUsageButton
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(
        array $row,
        string|null $href,
        string $label,
        string $title,
        string|null $icon,
        string $attributes,
    ): string {
        $sql   = <<<'SQL'
  SELECT COUNT(*) AS cnt 
    FROM tl_page 
   WHERE includeLayout = 1 AND (layout = :layoutId OR subpageLayout = :layoutId)
SQL;
        $usage = $this->connection->executeQuery($sql, ['layoutId' => $row['id']])->fetchOne();

        return sprintf(
            '<a href="%s" title="%s"%s>(%s)</a> ',
            $this->urlGenerator->generate(LayoutUsageController::class, ['layoutId' => $row['id']]),
            sprintf(
                $this->translator->trans('tl_layout.hofff_layoutusage', [], 'contao_hofff_layoutusage'),
                $row['name'],
                $row['id'],
            ),
            $attributes,
            (string) $usage,
        );
    }
}
