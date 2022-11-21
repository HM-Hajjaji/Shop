<?php

namespace App\MessageHandler;

use App\Message\TestMessage;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TestMessageHandler
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->entityManager = $manager;
    }

    public function __invoke(TestMessage $message)
    {
        $this->entityManager->persist($message->getCat());
        $this->entityManager->flush();
    }
}
