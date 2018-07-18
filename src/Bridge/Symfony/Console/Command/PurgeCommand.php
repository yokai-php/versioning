<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Symfony\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yokai\Versioning\Purge\PurgerInterface;

class PurgeCommand extends Command
{
    /**
     * @var PurgerInterface
     */
    private $purger;

    public function __construct(PurgerInterface $purger)
    {
        parent::__construct(null);
        $this->purger = $purger;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('yokai:versioning:purge')
            ->setDescription('Purge versions')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->purger->purge();

        if ($output->isVeryVerbose()) {
            $output->writeln(
                sprintf('<info>Total purged versions : </info>%d<info>.</info>', $count)
            );
        }
    }
}
