<?php

namespace ChangeSetTestContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use ChangeSet\IdentityMap\SimpleIdentityMap;
use ChangeSetTestAsset\Stub\SampleIdentityExtractor;
use ChangeSetTestAsset\Stub\SampleTypeResolver;
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
     * @var string[]
     */
    private $subTypesMap = [];

    /**
     * @Given a subtype :subType for type :originalType
     *
     * @param string $originalType
     * @param string $subType
     */
    public function aSubtypeForType($originalType, $subType)
    {
        $this->subTypesMap[$subType] = $originalType;
    }

    /**
     * @Given a new IdentityMap with an IdentitySerializer
     */
    public function aNewIdentityMapWithAnIdentitySerializer()
    {
        $this->identityMap = new SimpleIdentityMap(
            new SampleIdentityExtractor(),
            new SampleTypeResolver($this->subTypesMap)
        );
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
        $this->createClassIfNotExists($className);

        $object = new $className;

        $object->identity = $identity;

        $this->objects[$name] = $object;
    }

    /**
     * @Given a new entity :name with the identity of :identity
     *
     * @param string $name
     * @param string $identity
     */
    public function aNewEntityWithTheIdentityOf($name, $identity)
    {
        $object = new stdClass();

        $object->identity = $this->identities[$identity];

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
            $this->identities[$name] = json_decode($value, true);

            return;
        }

        $this->createClassIfNotExists($type);

        $identity = new $type;

        foreach (json_decode($value, true) as $key => $value) {
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
        $this->createClassIfNotExists($type);

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
     * @Then I can retrieve object :name by class :className and the complex identity :identityName
     *
     * @param string $name
     * @param string $className
     * @param string $identityName
     */
    public function iCanRetrieveObjectByClassAndComplexIdentity($name, $className, $identityName)
    {
        if ($this->objects[$name] !== $this->identityMap->getObject($className, $this->identities[$identityName])) {
            $object = $this->identityMap->getObject($className, $this->identities[$identityName]);

            throw new UnexpectedValueException(sprintf(
                'Could not find object matching "%s" of type "%s" with identity "%s": "%s" found instead',
                $name,
                $className,
                var_export($this->identities[$identityName], true),
                $object ? get_class($object) : null
            ));
        }
    }

    /**
     * @Then I cannot retrieve object :name by class :className and the complex identity :identityName
     *
     * @param string $name
     * @param string $className
     * @param string $identityName
     */
    public function iCannotRetrieveObjectByClassAndComplexIdentity($name, $className, $identityName)
    {
        /*if (! $this->identityMap->hasIdentity($className, $this->identities[$identityName])) {
            // assumes that no other object with the same identity is stored in the identity map
            throw new UnexpectedValueException(sprintf(
                'Identity "%s" was not expected to be found in the identity map',
                $identityName
            ));
        }*/

        if ($this->objects[$name] === $this->identityMap->getObject($className, $this->identities[$identityName])) {
            throw new UnexpectedValueException(sprintf(
                'Object "%s" of type "%s" was not expected to be found via identity "%s"',
                $name,
                $className,
                $identityName
            ));
        }
    }

    /**
     * @Then identity :identityName of type :className does exist in the identity map
     *
     * @param string $identityName
     * @param string $className
     */
    public function identityOfTypeDoesExistInTheIdentityMap($identityName, $className)
    {
        if (! $this->identityMap->hasIdentity($className, $identityName)) {
            throw new UnexpectedValueException(sprintf(
                'Identity "%s" was expected to exist in the identity map',
                $identityName
            ));
        }
    }

    /**
     * @Then identity :identityName of type :className does not exist in the identity map
     *
     * @param string $identityName
     * @param string $className
     */
    public function identityOfTypeDoesNotExistInTheIdentityMap($identityName, $className)
    {
        if ($this->identityMap->hasIdentity($className, $identityName)) {
            throw new UnexpectedValueException(sprintf(
                'Identity "%s" was not expected to exist in the identity map',
                $identityName
            ));
        }
    }

    /**
     * @Then object :name does exist in the identity map
     *
     * @param string $name
     */
    public function objectDoesExistInTheIdentityMap($name)
    {
        if (! $this->identityMap->hasObject($this->objects[$name])) {
            throw new UnexpectedValueException(sprintf(
                'Object "%s" was expected to exist in the identity map',
                $name
            ));
        }
    }

    /**
     * @Then object :name does not exist in the identity map
     *
     * @param string $name
     */
    public function objectDoesNotExistInTheIdentityMap($name)
    {
        if ($this->identityMap->hasObject($this->objects[$name])) {
            throw new UnexpectedValueException(sprintf(
                'Object "%s" was not expected to exist in the identity map',
                $name
            ));
        }
    }

    /**
     * @Then I can retrieve complex identity :identityName by object :objectName
     *
     * @param string $identityName
     * @param string $objectName
     */
    public function iCanRetrieveComplexIdentityByObject($identityName, $objectName)
    {
        if (! $this->identityMap->hasObject($this->objects[$objectName])) {
            throw new UnexpectedValueException(sprintf(
                'Object "%s" could not be found in the identity map',
                $objectName
            ));
        }

        if ($this->identities[$identityName] != $this->identityMap->getIdentity($this->objects[$objectName])) {
            $identity = $this->identityMap->getIdentity($this->objects[$objectName]);

            throw new UnexpectedValueException(sprintf(
                'Could not find identity matching object "%s" of type "%s": "%s" found instead',
                $objectName,
                get_class($this->objects[$objectName]),
                var_export($identity, true)
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

    /**
     * @Then I cannot retrieve identity :identity by object :name
     *
     * @param string $identity
     * @param string $name
     */
    public function iCannotRetrieveIdentityByObject($identity, $name)
    {
        if ($identity == $this->identityMap->getIdentity($this->objects[$name])) {
            $identity = $this->identityMap->getIdentity($this->objects[$name]);

            throw new UnexpectedValueException(sprintf(
                'Wasn\'t expecting to find identity "%s" matching object "%s"',
                $identity,
                $name
            ));
        }
    }

    /**
     * @Then I remove the entity :name from the identity map
     *
     * @param string $name
     */
    public function iRemoveTheEntityFromTheIdentityMap($name)
    {
        if (! $this->identityMap->removeObject($this->objects[$name])) {
            throw new UnexpectedValueException(sprintf(
                'Could not remove entity "%s" from the identity map: maybe it was already removed?',
                $name
            ));
        }
    }

    /**
     * @Then I remove the identity :identity of type :className from the identity map
     *
     * @param string $identity
     * @param string $className
     */
    public function iRemoveTheIdentityOfTypeFromTheIdentityMap($identity, $className)
    {
        if (! $this->identityMap->removeByIdentity($className, $identity)) {
            throw new UnexpectedValueException(sprintf(
                'Could not remove identity "%s" for type "%s" from the identity map: maybe it was already removed?',
                $identity,
                $className
            ));
        }
    }

    /**
     * @Then I cannot remove the identity :identity of type :className from the identity map
     *
     * @param string $identity
     * @param string $className
     */
    public function iCannotRemoveTheIdentityOfTypeFromTheIdentityMap($identity, $className)
    {
        if ($this->identityMap->removeByIdentity($className, $identity)) {
            throw new UnexpectedValueException(sprintf(
                'Was not expected to be able to remove identity "%s" for type "%s"'
                . ' from the identity map: operation should fail',
                $identity,
                $className
            ));
        }
    }

    private function createClassIfNotExists($className)
    {
        if (! class_exists($className)) {
            eval('class ' . $className . ' extends stdClass {}');
        }
    }
}
