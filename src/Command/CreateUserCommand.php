<?php

namespace App\Command;

use App\Entity\Category;
use App\Message\TestMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:create-category',
    description: 'Creates a new user.',
)]
class CreateUserCommand extends Command
{
    private SluggerInterface $slug;
    private MessageBusInterface $messageBus;
    public function __construct(SluggerInterface $slugger,MessageBusInterface $bus)
    {
        parent::__construct();
        $this->slug = $slugger;
        $this->messageBus = $bus;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('now your creating a new user')
            ->addArgument('name', InputArgument::REQUIRED, 'Enter Name  : ')
            ->addArgument('desc', InputArgument::REQUIRED, 'Enter Name Desc : ')
//            ->addOption('hamza', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->comment('create category start');

        $name = $input->getArgument('name');
        $desc = $input->getArgument('desc');

        $io->note(sprintf('You passed an argument name: %s', $name));
        $io->note(sprintf('You passed an argument desc: %s', $desc));

        $cat = new Category();
        $cat->setName($name);
        $cat->setDate(new \DateTime());
        $cat->setSlug($this->slug->slug($name,"-"));
        $cat->setDescription($desc);
        if ($cat){
            $this->messageBus->dispatch(new TestMessage($cat));
        }

        $io->success('your createsd new category with command successfully');
//        return Command::SUCCESS;
        exit();
    }
}
