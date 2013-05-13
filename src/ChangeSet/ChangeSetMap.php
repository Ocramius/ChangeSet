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

/**
 * A map of {@see \ChangeSet\ChangeInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @todo implementation
 */
class ChangeSetMap
{
    /**
     * @var string[] map of object identifiers to registered objects
     */
    protected $identifiersMap = array();

    /**
     * @var \ChangeSet\ChangeInterface[] map of registered objects indexed by object hash
     */
    protected $objectMap = array();

    public function find($identifier)
    {
        return isset($this->identifiersMap[$identifier])
            ? $this->objectMap[$this->identifiersMap[$identifier]]->getObject()
            : null;
    }

    public function contains($object)
    {
        return isset($this->objectMap[spl_object_hash($object)]);
    }

    public function add(ChangeInterface $change)
    {
        $object = $change->getObject();


    }

    public function computeScheduleInsertsChangeSets(){}
    private function computeSingleEntityChangeSet($entity){}
    private function executeExtraUpdates(){}
    public function getEntityChangeSet($entity){}
    public function computeChangeSet($entity){}
    public function computeChangeSets(){}
    private function computeAssociationChanges($assoc, $value){}
    private function persistNew($class, $entity){}
    public function recomputeSingleEntityChangeSet($entity){}
    private function executeInserts($class){}
    private function executeUpdates($class){}
    private function executeDeletions($class){}
    private function getCommitOrder(array $entityChangeSet = null){}
    public function scheduleForInsert($entity){}
    public function isScheduledForInsert($entity){}
    public function scheduleForUpdate($entity){}
    public function scheduleExtraUpdate($entity, array $changeset){}
    public function isScheduledForUpdate($entity){}
    public function isScheduledForDirtyCheck($entity){}
    public function scheduleForDelete($entity){}
    public function isScheduledForDelete($entity){}
    public function isEntityScheduled($entity){}
    public function addToIdentityMap($entity){}
    public function getEntityState($entity, $assume = null){}
    public function removeFromIdentityMap($entity){}
    public function getByIdHash($idHash, $rootClassName){}
    public function tryGetByIdHash($idHash, $rootClassName){}
    public function isInIdentityMap($entity){}
    public function containsIdHash($idHash, $rootClassName){}
    public function persist($entity){}
    private function doPersist($entity, array &$visited){}
    public function remove($entity){}
    private function doRemove($entity, array &$visited){}
    public function merge($entity){}
    private function flattenIdentifier($class, $id){}
    private function doMerge($entity, array &$visited, $prevManagedCopy = null, $assoc = null){}
    public function detach($entity){}
    private function doDetach($entity, array &$visited, $noCascade = false){}
    public function refresh($entity){}
    private function doRefresh($entity, array &$visited){}
    private function cascadeRefresh($entity, array &$visited){}
    private function cascadeDetach($entity, array &$visited){}
    private function cascadeMerge($entity, $managedCopy, array &$visited){}
    private function cascadePersist($entity, array &$visited){}
    private function cascadeRemove($entity, array &$visited){}
    public function lock($entity, $lockMode, $lockVersion = null){}
    public function getCommitOrderCalculator(){}
    public function clear($entityName = null){}
    public function scheduleOrphanRemoval($entity){}
    public function scheduleCollectionDeletion($coll){}
    public function isCollectionScheduledForDeletion($coll){}
    private function newInstance($class){}
    public function createEntity($className, array $data, &$hints = array()){}
    public function triggerEagerLoads(){}
    public function loadCollection($collection){}
    public function getIdentityMap(){}
    public function getOriginalEntityData($entity){}
    public function setOriginalEntityData($entity, array $data){}
    public function setOriginalEntityProperty($oid, $property, $value){}
    public function getEntityIdentifier($entity){}
    public function getSingleIdentifierValue($entity){}
    public function tryGetById($id, $rootClassName){}
    public function scheduleForDirtyCheck($entity){}
    public function hasPendingInsertions(){}
    public function size(){}
    public function getEntityPersister($entityName){}
    public function getCollectionPersister(array $association){}
    public function registerManaged($entity, array $id, array $data){}
    public function clearEntityChangeSet($oid){}
    public function propertyChanged($entity, $propertyName, $oldValue, $newValue){}
    public function getScheduledEntityInsertions(){}
    public function getScheduledEntityUpdates(){}
    public function getScheduledEntityDeletions(){}
    public function getScheduledCollectionDeletions(){}
    public function getScheduledCollectionUpdates(){}
    public function initializeObject($obj){}
    private static function objToStr($obj){}
    public function markReadOnly($object){}
    public function isReadOnly($object){}
    private function dispatchOnFlushEvent(){}
    private function dispatchPostFlushEvent(){}
}
