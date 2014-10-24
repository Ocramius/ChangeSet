<?php

namespace ChangeSetTestContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use ChangeSet\UnitOfWork\SimpleUnitOfWork;
use Zend\EventManager\EventManager;

/**
 * Behat context for {@see \ChangeSet\UnitOfWork\SimpleUnitOfWork} implementation exploration
 */
class UnitOfWorkContext implements Context, SnippetAcceptingContext
{
    /**
     * @var object[] indexed by name
     */
    private $objects = [];

    /**
     * @var \ChangeSet\UnitOfWork\UnitOfWorkInterface
     */
    private $unitOfWork;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given a new UnitOfWork
     */
    public function aNewUnitofwork()
    {
        $this->unitOfWork = new SimpleUnitOfWork(new EventManager());
    }

    /**
     * @Given a new object :name
     */
    public function aNewObject($name)
    {
        throw new PendingException();
    }

    /**
     * @When I persist the object :name
     */
    public function iPersistTheObject($name)
    {
        throw new PendingException();
    }

    /**
     * @Then the object :name must be managed by the UnitOfWork
     */
    public function theObjectMustBeManagedByTheUnitofwork($name)
    {
        throw new PendingException();
    }

    /**
     * @When I remove the object :name
     */
    public function iRemoveTheObject($name)
    {
        throw new PendingException();
    }

    /**
     * @Then the object :name must be unknown to the UnitOfWork
     */
    public function theObjectMustBeUnknownToTheUnitofwork($name)
    {
        throw new PendingException();
    }
}
