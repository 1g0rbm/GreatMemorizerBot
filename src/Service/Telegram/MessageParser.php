<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Message\CallbackQuery;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Telegram\Message\From;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Exception\Telegram\Message\ParseMessageException;
use Ig0rbm\Memo\Service\InitializeAccountService;

class MessageParser
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var TextParser */
    private $textParser;

    /** @var InitializeAccountService */
    private $initializeAccount;

    public function __construct(
        ValidatorInterface $validator,
        TextParser $textParser,
        InitializeAccountService $initializeAccount
    ) {
        $this->validator         = $validator;
        $this->textParser        = $textParser;
        $this->initializeAccount = $initializeAccount;
    }

    /**
     * @throws ORMException
     */
    public function createMessage(string $message): MessageFrom
    {
        $request = json_decode($message, true);

        if (!isset($request['message']) &&
            !isset($request['edited_message']) &&
            !isset($request['callback_query'], $request['callback_query']['message'])
        ) {
            throw ParseMessageException::becauseInvalidParameter('No message parameter');
        }

        $msgRaw = $request['message'] ?? $request['edited_message'] ?? $request['callback_query']['message'];

        $message = $this->createMessageFrom($msgRaw);

        if (isset($msgRaw['reply_to_message'])) {
            $message->setReply($this->createMessageFrom($msgRaw['reply_to_message']));
        }

        if (isset($request['callback_query'])) {
            $message->setCallbackQuery($this->createCallbackQuery($request['callback_query']));
        }

        $this->validate($message);

        return $message;
    }

    /**
     * @param mixed[]
     * @return MessageFrom
     * @throws ORMException
     */
    private function createMessageFrom(array $msgRaw): MessageFrom
    {
        if (isset($msgRaw['text']) === false) {
            throw ParseMessageException::becauseInvalidParameter('There isn\'t "text" parameter');
        }

        $account = $this->initializeAccount->initialize($this->createChat($msgRaw['chat']));
        $from = $this->createFrom($msgRaw['from']);

        $message = new MessageFrom();
        $message->setMessageId($msgRaw['message_id']);
        $message->setChat($account->getChat());
        $message->setFrom($from);
        $message->setDate($msgRaw['date']);
        $message->setText($this->textParser->parse($msgRaw['text']));

        return $message;
    }

    /**
     * @param array $chatRaw
     * @return Chat
     */
    private function createChat(array $chatRaw): Chat
    {
        $chat = new Chat();
        $chat->setId($chatRaw['id']);
        $chat->setType($chatRaw['type']);
        $chat->setFirstName($chatRaw['first_name'] ?? null);
        $chat->setLastName($chatRaw['last_name'] ?? null);
        $chat->setUsername($chatRaw['username'] ?? null);

        $this->validate($chat);

        return $chat;
    }

    private function createFrom(array $fromRaw): From
    {
        $from = new From();
        $from->setId($fromRaw['id']);
        $from->setIsBot($fromRaw['is_bot']);
        $from->setFirstName($fromRaw['first_name'] ?? null);
        $from->setLastName($fromRaw['last_name'] ?? null);
        $from->setUsername($fromRaw['username'] ?? null);
        $from->setLanguageCode($fromRaw['language_code'] ?? null);

        $this->validate($from);

        return $from;
    }

    public function createCallbackQuery(array $rawCallbackQuery): CallbackQuery
    {
        $query = new CallbackQuery();
        $query->setId($rawCallbackQuery['id']);
        $query->setFrom($this->createFrom($rawCallbackQuery['from']));
        $query->setChatInstance($rawCallbackQuery['chat_instance']);
        $query->setData($this->textParser->parse($rawCallbackQuery['data']));

        return $query;
    }

    /**
     * @param Chat|From|MessageFrom $value
     */
    private function validate($value)
    {
        $errors = $this->validator->validate($value);
        if (count($errors) > 0) {
            throw ParseMessageException::becauseInvalidParameter((string)$errors);
        }
    }
}
