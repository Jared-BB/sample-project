<?php

declare(strict_types=1);

namespace App\Tests\Story\User;

use App\Tests\Factory\User\UserFactory;
use Zenstruck\Foundry\Story;

class UserStory extends Story
{
    public function build(): void
    {
        $user = UserFactory::createOne(['email' => 'test@attendo.com', 'password' => 'PasswordOk1']);
        $this->addState('user', $user);
    }
}
