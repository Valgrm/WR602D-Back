<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'get-all:users',
    description: 'Use to get all users',
)]
class GetAllUsersCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');
        $users = $this->userRepository->findAll();

        if (count($users) === 0) {
            $output->write('There is no users registered yet');

            return Command::SUCCESS;
        }

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $table = new Table($output);
        $table->setHeaders(['Id', 'Username', 'Mail']);

        foreach ($users as $user) {
            $table->addRow([
                $user->getId(),
                $user->getUsername(),
                $user->getEmail(),
            ]);
        }

        $table->render();
        $output->writeln(sprintf('Total users: %d', count($users)));

        return Command::SUCCESS;
    }
}
