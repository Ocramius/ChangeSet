<?php

namespace ChangeSetTestContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use ChangeSet\Committer\SimpleLoggingCommitter;
use ChangeSet\IdentityMap\IdentityMap;
use ChangeSet\IdentityMap\SimpleIdentityMap;
use ChangeSet\UnitOfWork\SimpleUnitOfWork;
use LogicException;
use stdClass;
use UnexpectedValueException;
use Zend\EventManager\EventManager;

/**
 * Behat context for {@see \ChangeSet\IdentityMap\SimpleIdentityMap} implementation exploration
 */
class IdentityMapContext implements Context, SnippetAcceptingContext
{
    /**
     * @var \stdClass[] indexed by name
     */
    private $objects = [];

    /**
     * @var SimpleIdentityMap|null
     */
    private $identityMap;

    /**
     * @Given a new IdentityMap with an IdentifierSerializer
     */
    public function aNewIdentitymapWithAnIdentifierserializer()
    {
        $this->identityMap = new SimpleIdentityMap();
    }

    /**
     * @Given a new entity :name of type :className with identifier :identifier
     *
     * @param string $name
     * @param string $identifier
     */
    public function aNewEntityWithIdentifier($name, $className, $identifier)
    {
        $object = new $className;

        $object->identifier = $identifier;

        $this->objects[$name] = $object;
    }

    /**
     * @When I store the entity :name in the identity map
     *
     * @param string $name
     */
    public function iStoreTheEntityInTheIdentityMap($name)
    {
        $this->identityMap->add($this->objects[$name]);
    }

    /**
     * @When I store the entity :name in the identity map with identifier :name
     *
     * @param string $name
     * @param string $identifier
     */
    public function iStoreTheEntityInTheIdentityMapWithIdentifier($name, $identifier)
    {
        $this->identityMap->add($this->objects[$name], $identifier);
    }

    /**
     * @Then I can retrieve object :name by class :className and identifier :identifier
     *
     * @param string $name
     * @param string $className
     * @param string $identifier
     */
    public function iCanRetrieveObjectByIdentifier($name, $className, $identifier)
    {
        if ($this->objects[$name] !== $this->identityMap->getObject($className, $identifier)) {
            $object = $this->identityMap->getObject($className, $identifier);

            throw new UnexpectedValueException(sprintf(
                'Could not find object matching "%s" of type "%s" with identity "%s": "%s" found instead',
                $name,
                $className,
                $identifier,
                $object ? get_class($object) : null
            ));
        }
    }

    /**
     * @Then I can retrieve identifier :identifier by object :name
     *
     * @param string $identifier
     * @param string $name
     */
    public function iCanRetrieveIdentifierByObject($identifier, $name)
    {
        if ($identifier !== $this->identityMap->getIdentity($this->objects[$name])) {
            $identity = $this->identityMap->getIdentity($this->objects[$name]);

            throw new UnexpectedValueException(sprintf(
                'Could not find identity "%s" matching object "%s": "%s" found instead',
                $identifier,
                $name,
                gettype($identity)
            ));
        }
    }
}
