<?php

namespace ChangeSetTestContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use ChangeSet\Committer\SimpleLoggingCommitter;
use ChangeSet\UnitOfWork\SimpleUnitOfWork;
use LogicException;
use stdClass;
use UnexpectedValueException;
use Zend\EventManager\EventManager;

/**
 * Behat context for {@see \ChangeSet\UnitOfWork\SimpleUnitOfWork} implementation exploration
 */
class UnitOfWorkContext implements Context, SnippetAcceptingContext
{
    /**
     * @var \stdClass[] indexed by name
     */
    private $objects = [];

    /**
     * @var \ChangeSet\UnitOfWork\SimpleUnitOfWork
     */
    private $unitOfWork;

    /**
     * @var array|null
     */
    private $lastCommit;

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
     *
     * @param string $name
     */
    public function aNewObject($name)
    {
        $object = new stdClass();

        $object->name         = $name;
        $this->objects[$name] = $object;
    }

    /**
     * @When I persist the object :name
     *
     * @param string $name
     */
    public function iPersistTheObject($name)
    {
        $this->unitOfWork->registerNew($this->objects[$name]);
    }

    /**
     * @When I remove the object :name
     *
     * @param string $name
     */
    public function iRemoveTheObject($name)
    {
        $this->unitOfWork->registerRemoved($this->objects[$name]);
    }
    /**
     * @When I register the object :name
     *
     * @param string $name
     */
    public function iRegisterTheObject($name)
    {
        $this->unitOfWork->registerClean($this->objects[$name]);
    }

    /**
     * @Then the object :name must be marked as :state
     *
     * @param string $name
     * @param string $state
     */
    public function theObjectMustBeMarkedAs($name, $state)
    {
        throw new PendingException;
    }

    /**
     * @Then I cannot remove the object :name
     *
     * @param string $name
     */
    public function iCannotRemoveTheObject($name)
    {
        try {
            $this->unitOfWork->registerRemoved($this->objects[$name]);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        throw new UnexpectedValueException(sprintf(
            'Was expecting removal of object "%s" to fail',
            $name
        ));
    }

    /**
     * @Then the object :name must be managed by the UnitOfWork
     *
     * @param string $name
     */
    public function theObjectMustBeManagedByTheUnitofwork($name)
    {
        if (! $this->unitOfWork->contains($this->objects[$name])) {
            throw new UnexpectedValueException(sprintf(
                'Expect object "%s" to be contained in the UnitOfWork',
                $name
            ));
        }
    }

    /**
     * @Then the object :name must be unknown to the UnitOfWork
     *
     * @param string $name
     */
    public function theObjectMustBeUnknownToTheUnitofwork($name)
    {
        if ($this->unitOfWork->contains($this->objects[$name])) {
            throw new UnexpectedValueException(sprintf(
                'Did not expect object "%s" to be contained in the UnitOfWork',
                $name
            ));
        }
    }

    /**
     * @When I commit( again)
     */
    public function iCommit()
    {
        $committer = new SimpleLoggingCommitter();

        $this->unitOfWork->commit($committer);

        $this->lastCommit = $committer->operations;
    }

    /**
     * @Then the object :name is in the :changeType records
     *
     * @param string $name
     * @param string $changeType
     */
    public function theObjectIsInTheRecords($name, $changeType)
    {
        if (null === $this->lastCommit) {
            throw new LogicException('Did not commit yet, therefore cannot check the last commit');
        }

        foreach ($this->lastCommit as $record) {
            if ($this->objects[$name] === $record['object']) {
                if ($record['type'] === $changeType) {
                    return;
                }

                throw new UnexpectedValueException(sprintf(
                    'Expected object "%s" to be found in change type "%s", found it in change type "%s" instead',
                    $name,
                    $changeType,
                    $record['type']
                ));
            }
        }

        throw new UnexpectedValueException(sprintf('Could not find to find object "%s" in the commit', $name));
    }

    /**
     * @Then the commit is empty
     */
    public function theCommitIsEmpty()
    {
        $this->theNumberOfRecordsInTheCommitIs(0);
    }

    /**
     * @Then the number of records in the commit is :count
     *
     * @param int $count
     */
    public function theNumberOfRecordsInTheCommitIs($count)
    {
        if (null === $this->lastCommit) {
            throw new LogicException('Did not commit yet, therefore cannot check the last commit');
        }

        if (count($this->lastCommit) != $count) {
            throw new UnexpectedValueException(sprintf(
                'Expected committed record count to be "%s", "%s" found',
                $count,
                count($this->lastCommit)
            ));
        }
    }

    /**
     * @Given I change the object :name
     *
     * @param string $name
     */
    public function iChangeTheObject($name)
    {
        $object = $this->objects[$name];

        $object->changedProperty = (isset($object->changedProperty) ? $object->changedProperty : '') . ' - changed';
    }
}
