<?php

declare(strict_types=1);

namespace App\Tests\Story\Access\Group;

use App\Tests\Factory\Access\Group\GroupFactory;
use Zenstruck\Foundry\Story;

class GroupStory extends Story
{
    public function build(): void
    {
        $group = GroupFactory::createOne(['name' => 'Attendo DEV']);
        $this->addState('group', $group);
    }
}
