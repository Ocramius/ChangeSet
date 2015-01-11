<?php

namespace ChangeSetTestContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use ChangeSet\IdentityMap\SimpleIdentityMap;
use stdClass;
use UnexpectedValueException;

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
     * @var object[]|array[]
     */
    private $identities = [];

    /**
     * @var SimpleIdentityMap|null
     */
    private $identityMap;

    /**
     * @Given a new IdentityMap with an IdentitySerializer
     */
    public function aNewIdentityMapWithAnIdentitySerializer()
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
        if (! class_exists($className)) {
            eval('class ' . $className . ' {}');
        }

        $object = new $className;

        $object->identity = $identity;

        $this->objects[$name] = $object;
    }

    /**
     * @Given a new complex identity :name of type :type and value :value
     *
     * @param string $name
     * @param string $type
     * @param string $value
     */
    public function aNewComplexIdentity($name, $type, $value)
    {
        if ('array' === strtolower($type)) {
            $this->identities[$name] = json_decode($value);

            return;
        }

        if (! class_exists($type)) {
            eval('class ' . $type . ' {}');
        }

        $identity = new $type;

        foreach (json_decode($value) as $key => $value) {
            $identity->$key = $value;
        }

        $this->identities[$name] = $identity;
    }

    /**
     * @Given a new entity :name of type :type with the identity of :identityName
     *
     * @param string $name
     * @param string $type
     * @param string $identityName
     */
    public function aNewEntityWithTheIdentity($name, $type, $identityName)
    {
        if (! class_exists($type)) {
            eval('class ' . $type . ' {}');
        }

        $entity = new $type;
        $entity->identity = $this->identities[$identityName];

        $this->objects[$name] = $entity;
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
     * @Then I cannot retrieve object :name by class :className and identity :identity
     *
     * @param string $name
     * @param string $className
     * @param string $identity
     */
    public function iCannotRetrieveObjectByIdentity($name, $className, $identity)
    {
        if ($this->objects[$name] === $this->identityMap->getObject($className, $identity)) {
            throw new UnexpectedValueException(sprintf(
                'Didn\'t expect to find object matching "%s" of type "%s" with identity "%s"',
                $name,
                $className,
                $identity
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
                gettype($identity)
            ));
        }
    }
}
