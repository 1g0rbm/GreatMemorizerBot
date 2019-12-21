<?php

namespace Ig0rbm\Memo\Command\ReplyKeyboard;

use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReplyKeyboardUpdateCommand extends Command
{
    private const NAME = 'memo:reply-keyboard:update';

    private AccountRepository $accountRepository;

    private EntityFlusher $flusher;

    public function __construct(AccountRepository $accountRepository, EntityFlusher $flusher)
    {
        parent::__construct(self::NAME);

        $this->accountRepository = $accountRepository;
        $this->flusher           = $flusher;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Mark all keyboards for updating')
            ->setHelp('The command allows you to update ReplyKeyboard for all accounts.');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $accounts = $this->accountRepository->findAll();
        foreach ($accounts as $account) {
            $account->setNeedKeyboardUpdate(true);
        }

        $this->flusher->flush();
    }
}
