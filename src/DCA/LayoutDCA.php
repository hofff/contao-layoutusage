<?php

declare(strict_types=1);

namespace Hofff\Contao\LayoutUsage\DCA;

use Contao\Backend;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

final class LayoutDCA
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[AsCallback('tl_layout', 'list.operations.hofff_layoutusage_btn.button')]
    public function getUsageButton(
        array $row,
        string|null $href,
        string $label,
        string $title,
        string|null $icon,
        string $attributes,
    ): string {
        $sql   = 'SELECT COUNT(*) AS cnt FROM tl_page WHERE includeLayout = 1 AND layout = ?';
        $usage = $this->connection->executeQuery($sql, [$row['id']])->fetchOne();

        return sprintf(
            '<a href="%s" title="%s"%s>(%s)</a> ',
            Backend::addToUrl(((string) $href) . '&id=' . $row['id']),
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
