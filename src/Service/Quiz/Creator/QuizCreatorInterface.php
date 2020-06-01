<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz\Creator;

use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;

interface QuizCreatorInterface
{
    public function type(): string;

    public function create(Chat $chat): Quiz;
}
