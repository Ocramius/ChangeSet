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
     * @Given a new IdentityMap with an IdentitySerializer
     */
    public function aNewIdentitymapWithAnIdentityserializer()
    {
        $this->identityMap = new SimpleIdentityMap();
    }

    /**
     * @Given a new entity :name of type :className with identity :identity
     *
     * @param string $name
     * @param string $className
     * @param string $identity
     */
    public function aNewEntityWithIdentity($name, $className, $identity)
    {
        $object = new $className;

        $object->identity = $identity;

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
     * @When I store the entity :name in the identity map with identity :identity
     *
     * @param string $name
     * @param string $identity
     */
    public function iStoreTheEntityInTheIdentityMapWithIdentity($name, $identity)
    {
        $this->identityMap->add($this->objects[$name], $identity);
    }

    /**
     * @Then I can retrieve object :name by class :className and identity :identity
     *
     * @param string $name
     * @param string $className
     * @param string $identity
     */
    public function iCanRetrieveObjectByIdentity($name, $className, $identity)
    {
        if ($this->objects[$name] !== $this->identityMap->getObject($className, $identity)) {
            $object = $this->identityMap->getObject($className, $identity);

            throw new UnexpectedValueException(sprintf(
                'Could not find object matching "%s" of type "%s" with identity "%s": "%s" found instead',
                $name,
                $className,
                $identity,
                $object ? get_class($object) : null
            ));
        }
    }

    /**
     * @Then I can retrieve identity :identity by object :name
     *
     * @param string $identity
     * @param string $name
     */
    public function iCanRetrieveIdentityByObject($identity, $name)
    {
        if ($identity !== $this->identityMap->getIdentity($this->objects[$name])) {
            $identity = $this->identityMap->getIdentity($this->objects[$name]);

            throw new UnexpectedValueException(sprintf(
                'Could not find identity "%s" matching object "%s": "%s" found instead',
                $identity,
                $name,
                gettype()
            ));
        }
    }
}
