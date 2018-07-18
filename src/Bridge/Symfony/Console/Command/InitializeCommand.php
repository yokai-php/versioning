<?php declare(strict_types=1);

namespace Yokai\Versioning\Bridge\Symfony\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yokai\Versioning\Initialize\Initializer;
use Yokai\Versioning\TypesConfig;

class InitializeCommand extends Command
{
    /**
     * @var Initializer
     */
    private $initializer;

    /**
     * @var TypesConfig
     */
    private $typesConfig;

    public function __construct(Initializer $initializer, TypesConfig $typesConfig)
    {
        parent::__construct(null);
        $this->initializer = $initializer;
        $this->typesConfig = $typesConfig;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('yokai:versioning:initialize')
            ->setDescription('Initialize versions for versionable resources.')
            ->addArgument('type', InputArgument::OPTIONAL, 'A resource type')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'All resources')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $all = $input->getOption('all');

        if (null === $type && false === $all) {
            throw new RuntimeException('You must either provide "[type]" argument or "[-a|--all]" option.');
        }

        $types = [];
        if (null !== $type) {
            $types = [$type];
        } elseif ($all) {
            $types = $this->typesConfig->listResourceTypes();
        }

        foreach ($types as $type) {
            $this->initializer->initialize(
                $this->typesConfig->getResourceClass($type)
            );
        }
    }
}
