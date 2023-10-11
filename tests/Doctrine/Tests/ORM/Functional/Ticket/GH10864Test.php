<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Tests\OrmFunctionalTestCase;

class GH10864Test extends OrmFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpEntitySchema([
            GH10864ParentEntity::class,
            GH10864ChildEntity::class,
        ]);
    }

    public function testChildrenInsertedInTheSameOrderTheyPersisted(): void
    {
        $parent = new GH10864ParentEntity();

        $child1 = new GH10864ChildEntity();
        $child1->parent = $parent;
        $this->_em->persist($child1);

        $child2 = new GH10864ChildEntity();
        $child2->parent = $parent;
        $this->_em->persist($child2);

        $this->_em->persist($parent);

        $this->_em->flush();

        self::assertSame(1, $child1->id);
        self::assertSame(2, $child2->id);
    }
}

/**
 * @ORM\Entity
 */
class GH10864ParentEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var ?int
     */
    public $id;

    /**
     * @ORM\OneToMany(targetEntity="GH10864ChildEntity", mappedBy="parent")
     *
     * @var \Doctrine\Common\Collections\Collection<int,GH10864ChildEntity>
     */
    public $children;
}

/**
 * @ORM\Entity
 */
class GH10864ChildEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var ?int
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="GH10864ParentEntity", inversedBy="children")
     *
     * @var GH10864ParentEntity
     */
    public $parent;
}
