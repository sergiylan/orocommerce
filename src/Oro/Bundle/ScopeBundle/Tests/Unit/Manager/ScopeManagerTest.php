<?php

namespace Oro\Bundle\ScopeBundle\Tests\Unit\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\EntityBundle\Provider\EntityFieldProvider;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Bundle\ScopeBundle\Manager\ScopeManager;
use Oro\Bundle\ScopeBundle\Manager\ScopeProviderInterface;

class ScopeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ScopeManager
     */
    protected $manager;

    /**
     * @var ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var EntityFieldProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityFieldProvider;

    public function setUp()
    {
        $this->registry = $this->getMock(ManagerRegistry::class);
        $this->entityFieldProvider = $this->getMockBuilder(EntityFieldProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->manager = new ScopeManager($this->registry, $this->entityFieldProvider);
    }

    public function tearDown()
    {
        unset($this->manager, $this->registry, $this->entityFieldProvider);
    }

    public function testFindScope()
    {
        $scope = new Scope();
        $provider = $this->getMock(ScopeProviderInterface::class);
        $provider->method('getCriteria')->willReturn(['fieldName' => 1]);

        $repository = $this->getMock(ObjectRepository::class);
        $repository->method('findOneBy')->with(['fieldName' => 1, 'fieldName2' => null])->willReturn($scope);

        $em = $this->getMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($repository);
        $this->registry->method('getManagerForClass')->willReturn($em);

        $this->entityFieldProvider->method('getRelations')->willReturn([
            ['name' => 'fieldName'],
            ['name' => 'fieldName2']
        ]);

        $this->manager->addProvider('testScope', $provider);
        $actualScope = $this->manager->find('testScope');
        $this->assertEquals($scope, $actualScope);
    }
}
