<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ChangeSet;

use ChangeSet\ChangeWriter\ChangeWriterInterface;
use ChangeSet\Comparator\ComparatorInterface;
use ChangeSet\IdentifierGenerator\IdentifierGeneratorInterface;

/**
 * A map of {@see \ChangeSet\ChangeInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @todo abstract remaining UoW methods
 * @todo replace exceptions with specific exception types
 * @todo use SPL data structures where meaningful to increase speed
 */
class ChangeSetMap
{
    const STATE_NEW     = 0;
    const STATE_MANAGED = 1;
    const STATE_REMOVED = 2;

    /**
     * @var \ChangeSet\IdentifierGenerator\IdentifierGeneratorInterface
     */
    protected $identifierGenerator;

    /**
     * @var \ChangeSet\Comparator\ComparatorInterface
     */
    protected $comparator;

    /**
     * @var \ChangeSet\ChangeWriter\ChangeWriterInterface
     */
    protected $changeWriter;

    /**
     * @var string[] map of object identifiers to registered objects
     */
    protected $identifiersMap = array();

    /**
     * @var \ChangeSet\ChangeInterface[] map of registered objects change sets indexed by object hash - lazily computed
     */
    protected $changes = array();

    /**
     * @var object[] map of registered objects indexed by object hash
     */
    protected $objectMap = array();

    /**
     * @var int[] map of object states indexed by object hash
     */
    protected $statesMap = array();

    public function __construct(
        IdentifierGeneratorInterface $identifierGenerator,
        ComparatorInterface $comparator,
        ChangeWriterInterface $changeWriter
    ) {
        $this->identifierGenerator = $identifierGenerator;
        $this->comparator          = $comparator;
        $this->changeWriter        = $changeWriter;
    }

    /**
     * Finds an object by its identifier
     */
    public function find($identifier)
    {
        return isset($this->identifiersMap[$identifier])
            ? $this->objectMap[$this->identifiersMap[$identifier]]->getObject()
            : null;
    }

    /**
     * Checks if an object exists in this collection
     */
    public function contains($object)
    {
        return isset($this->objectMap[spl_object_hash($object)]);
    }

    /**
     * Retrieves the change set for this object
     */
    public function getChanges($object)
    {
        $objectHash = spl_object_hash($object);

        if (!isset($this->statesMap[$objectHash])) {
            throw new \RuntimeException('Object ' . get_class($object) . '@' . $objectHash . ' unknown');
        }

        // @todo what to do for STATE_REMOVED and STATE_NEW ? Fake change object? Should the comparator generate that?
        // @todo should probably register noop changesets for STATE_REMOVED and STATE_NEW, so that the
        // @todo comparator can trigger extra checks on associations somehow - after all, insert and deletes are changes

        if (static::STATE_MANAGED !== $this->statesMap[$objectHash]) {
            return null;
        }

        return isset($this->changes[$objectHash])
            ? $this->changes[$objectHash]
            : $this->changes[$objectHash] = $this->comparator->createChange($this->objectMap[$objectHash]);
    }

    /**
     * Register the given object in the container with the given state
     *
     * @todo split into persist/merge/remove?
     */
    public function register($object, $state)
    {
        $objectHash = spl_object_hash($object);
        $identifier = $this->identifierGenerator->getIdentifier($object);

        if (isset($this->objectMap[$objectHash])) {
            throw new \RuntimeException('Object ' . get_class($object) . '@' . $objectHash . ' already registered');
        }

        if (!in_array($state, array(static::STATE_NEW, static::STATE_MANAGED, static::STATE_REMOVED, true))) {
            throw new \InvalidArgumentException('State ' . var_export($state, true) . ' unrecognized');
        }


        if (isset($this->identifiersMap[$identifier])) {
            throw new \RuntimeException('Object ' . get_class($object) . '#' . $identifier . ' conflicting');
        }

        $this->objectMap[$objectHash]      = $object;
        $this->identifiersMap[$identifier] = $objectHash;
        $this->statesMap[$objectHash]      = $state;

        if (static::STATE_MANAGED === $state) {
            // force first state to be registered
            $this->getChanges($object);
        }
    }

    /**
     * Forces the given change for the object
     */
    public function registerChange(ChangeInterface $change)
    {
        $object     = $change->getObject();
        $objectHash = spl_object_hash($object);

        if (!isset($this->objectMap[$objectHash])) {
            // @todo what's the state here? Is actually the state part of the change object?
            $this->register($object, static::STATE_MANAGED);
        }

        // @todo what to do for STATE_REMOVED and STATE_NEW? Probably just push it in silently...

        $this->changes[$objectHash] = $change;
    }

    /**
     * Removes the object from the current map (detach)
     */
    public function unRegister($object)
    {
        $objectHash = spl_object_hash($object);
        $identifier = $this->identifierGenerator->getIdentifier($object);

        if (!isset($this->statesMap[$objectHash])) {
            throw new \RuntimeException('Object ' . get_class($object) . '@' . $objectHash . ' unknown');
        }

        unset($this->changes[$objectHash], $this->statesMap[$objectHash], $this->identifiersMap[$identifier]);
    }

    /**
     * Computes all changes and retrieves them
     */
    public function commit()
    {
        return array_map(array($this, 'getChanges'), $this->objectMap);
    }

    /**
     * Reverts all changes
     */
    public function rollback()
    {
        foreach ($this->changes as $change) {
            $this->changeWriter->revert($change);
        }
    }

    /**
     * Cleans state by removing STATE_REMOVED, moving STATE_NEW to STATE_MANAGED and cleaning STATE_MANAGED
     */
    public function clean()
    {
        // @todo iterate over STATE_MANAGED and clean state of the various changes
        // @todo iterate over STATE_NEW and move to STATE_MANAGED
        // @todo iterate over STATE_REMOVED and remove
    }

    /**
     * Resets this instance
     *
     * @todo maybe better if we return a new instance instead?
     * @todo consider allowing clearing of only certain elements by object or class name
     */
    public function clear()
    {
        $this->objectMap = $this->changes = $this->identifiersMap = $this->statesMap = array();
    }

    //public function computeScheduleInsertsChangeSets(){}
    //private function computeSingleEntityChangeSet($entity){}
    //private function executeExtraUpdates(){}
    //public function getEntityChangeSet($entity){}
    //public function computeChangeSet($entity){}
    //public function computeChangeSets(){}
    //private function computeAssociationChanges($assoc, $value){}
    //private function persistNew($class, $entity){}
    //public function recomputeSingleEntityChangeSet($entity){}
    //private function executeInserts($class){}
    //private function executeUpdates($class){}
    //private function executeDeletions($class){}
    //private function getCommitOrder(array $entityChangeSet = null){}
    //public function scheduleForInsert($entity){}
    //public function isScheduledForInsert($entity){}
    //public function scheduleForUpdate($entity){}
    //public function scheduleExtraUpdate($entity, array $changeset){}
    //public function isScheduledForUpdate($entity){}
    //public function isScheduledForDirtyCheck($entity){}
    //public function scheduleForDelete($entity){}
    //public function isScheduledForDelete($entity){}
    //public function isEntityScheduled($entity){}
    //public function addToIdentityMap($entity){}
    //public function getEntityState($entity, $assume = null){}
    //public function removeFromIdentityMap($entity){}
    //public function getByIdHash($idHash, $rootClassName){}
    //public function tryGetByIdHash($idHash, $rootClassName){}
    //public function isInIdentityMap($entity){}
    //public function containsIdHash($idHash, $rootClassName){}
    //public function persist($entity){}
    //private function doPersist($entity, array &$visited){}
    //public function remove($entity){}
    //private function doRemove($entity, array &$visited){}
    //public function merge($entity){}
    //private function flattenIdentifier($class, $id){}
    //private function doMerge($entity, array &$visited, $prevManagedCopy = null, $assoc = null){}
    //public function detach($entity){}
    //private function doDetach($entity, array &$visited, $noCascade = false){}
    //public function refresh($entity){}
    //private function doRefresh($entity, array &$visited){}
    //private function cascadeRefresh($entity, array &$visited){}
    //private function cascadeDetach($entity, array &$visited){}
    //private function cascadeMerge($entity, $managedCopy, array &$visited){}
    //private function cascadePersist($entity, array &$visited){}
    //private function cascadeRemove($entity, array &$visited){}
    //public function lock($entity, $lockMode, $lockVersion = null){}
    //public function getCommitOrderCalculator(){}
    //public function clear($entityName = null){}
    //public function scheduleOrphanRemoval($entity){}
    //public function scheduleCollectionDeletion($coll){}
    //public function isCollectionScheduledForDeletion($coll){}
    //private function newInstance($class){}
    //public function createEntity($className, array $data, &$hints = array()){}
    //public function triggerEagerLoads(){}
    //public function loadCollection($collection){}
    //public function getIdentityMap(){}
    //public function getOriginalEntityData($entity){}
    //public function setOriginalEntityData($entity, array $data){}
    //public function setOriginalEntityProperty($objectHash, $property, $value){}
    //public function getEntityIdentifier($entity){}
    //public function getSingleIdentifierValue($entity){}
    //public function tryGetById($id, $rootClassName){}
    //public function scheduleForDirtyCheck($entity){}
    //public function hasPendingInsertions(){}
    //public function size(){}
    //public function getEntityPersister($entityName){}
    //public function getCollectionPersister(array $association){}
    //public function registerManaged($entity, array $id, array $data){}
    //public function clearEntityChangeSet($objectHash){}
    //public function propertyChanged($entity, $propertyName, $oldValue, $newValue){}
    //public function getScheduledEntityInsertions(){}
    //public function getScheduledEntityUpdates(){}
    //public function getScheduledEntityDeletions(){}
    //public function getScheduledCollectionDeletions(){}
    //public function getScheduledCollectionUpdates(){}
    //public function initializeObject($obj){}
    //private static function objToStr($obj){}
    //public function markReadOnly($object){}
    //public function isReadOnly($object){}
    //private function dispatchOnFlushEvent(){}
    //private function dispatchPostFlushEvent(){}
}
