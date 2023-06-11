<?php

declare(strict_types=1);

namespace App\Messenger\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

/** @noinspection PhpUnused */
final class FriendsFixture extends AbstractFixture
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
    }
}
