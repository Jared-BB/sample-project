<?php

namespace App\Tests\Story\User;

use App\Tests\Factory\User\UserFactory;
use App\User\Domain\ValueObject\Role;
use Zenstruck\Foundry\Story;

class AdminUserStory extends Story
{
    public function build(): void
    {
        $user = UserFactory::createOne(['email' => 'admin@test.com', 'password' => 'PasswordOk1', 'role' => Role::ADMIN->value]);
        $this->addState('admin_user', $user);

        $user = UserFactory::createOne(['email' => 'test@test.com', 'password' => 'PasswordOk1', 'role' => Role::AGENT->value]);
        $this->addState('agent_user', $user);
    }
}
