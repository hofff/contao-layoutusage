<?php

declare(strict_types=1);

namespace Hofff\Contao\LayoutUsage\Backend;

use Contao\BackendModule;
use Contao\Controller;
use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\DataContainer;
use Contao\Message;
use Contao\System;
use Contao\Template;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function count;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ModuleLayoutUsage extends BackendModule
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    protected Template $Template;

    /** @var array<string, mixed>|null */
    private array|null $layout = null;

    /** @var list<array<string, mixed>> */
    private array $usages = [];

    private Connection $connection;

    private ContaoCsrfTokenManager $csrfTokenManager;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(DataContainer|null $dataContainer = null)
    {
        parent::__construct($dataContainer);

        /** @psalm-suppress PropertyTypeCoercion */
        $this->connection = self::getContainer()->get('database_connection');
        /** @psalm-suppress PropertyTypeCoercion */
        $this->csrfTokenManager = self::getContainer()->get('contao.csrf.token_manager');
        /** @psalm-suppress PropertyTypeCoercion */
        $this->urlGenerator = self::getContainer()->get('router');
    }

    public function setLayout(int $layout): void
    {
        $result = $this->connection->executeQuery('SELECT id, name FROM tl_layout WHERE id = ?', [$layout]);
        if ($result->rowCount() === 0) {
            return;
        }

        /** @psalm-suppress PossiblyFalsePropertyAssignmentValue */
        $this->layout = $result->fetchAssociative();
    }

    public function generateFromDC(DataContainer $dataContainer): string
    {
        $this->setLayout((int) $dataContainer->id);

        return $this->generate();
    }

    public function generate(): string
    {
        if ($this->layout === null) {
            throw new RedirectResponseException(System::getReferer());
        }

        $this->usages = $this->getUsages((int) $this->layout['id']);
        if ($this->usages === []) {
            throw new RedirectResponseException(System::getReferer());
        }

        $this->strTemplate = 'be_hofff_layoutusage';

        return parent::generate();
    }

    protected function compile(): void
    {
        System::loadLanguageFile('tl_page');
        Controller::loadDataContainer('tl_page');

        $usages = $this->usages;
        $count  = count($usages);

        foreach ($usages as &$usage) {
            $usage['maintenanceMode'] = false;

            $count        += $usage['inherited'];
            $usage['icon'] = $this->addPageIcon($usage, '', blnReturnImage: true);
        }

        unset($usage);

        $this->Template->layout  = $this->layout;
        $this->Template->usages  = $usages;
        $this->Template->count   = $count;
        $this->Template->referer = self::getReferer(true);
        $this->Template->message = Message::generate();
        $this->Template->pageUrl = fn (int $pageId): string => $this->urlGenerator->generate(
            'contao_backend',
            [
                'do' => 'page',
                'pn' => $pageId,
                'rt' => $this->csrfTokenManager->getDefaultTokenValue(),
            ],
        );
        $this->Template->editUrl = fn (int $pageId): string => $this->urlGenerator->generate(
            'contao_backend',
            [
                'do'  => 'page',
                'act' => 'edit',
                'id'  => $pageId,
                'rt'  => $this->csrfTokenManager->getDefaultTokenValue(),
            ],
        );
    }

    /** @return list<array<string,mixed>> */
    private function getUsages(int $pageId): array
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

        $result = $this->connection->executeQuery($sql, [$pageId]);

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
}
