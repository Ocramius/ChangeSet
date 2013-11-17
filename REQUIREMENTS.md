# Requirements of ChangeSet

Here's a list of the requirements that this library should fulfill:

 - MUST provide a simple interface for getting snapshots of `object`s
    - `Snapshot SnapshotManager#getSnapshot(object $object)`
    - `null|array SnapshotManager#compareSnapshots(Snapshot $left, Snapshot $right)`
    - `null|array SnapshotManager#compareState(object $object, Snapshot $snapshot)`
    - comparison should be allowed via either *value* or *identity*, depending on user needs

 - `Snapshot` instances MUST:
    - be `Serializable`
    - contain a reference to the `object` it is referring to (`object Snapshot#getObject()`)
    - contain a `mixed` state (`mixed Snapshot#getState()`)
    - be immutable
    - be fully cloneable
    - not have dependencies to services
    - be comparable to the tracked `object` state `null|Snapshot Snapshot#compare(SnapshotManager $snapshotManager)`
    - be able to produce new snapshots of itself (`Snapshot Snapshot#freeze(SnapshotManager $snapshotManager)`)

 - It must provide a simple `ChangeSet` that allows tracking object state (already implemented so far)
    - addition of `object`s
    - removal of registered `object`s
    - change of registered `object`s
    - allow `cleanup` (will remove all removed `object`s, register all added `object`s and clean any change tracking
    - MUST be `Serializable`
    - MUST be very fast
    - must be fully cloneable
    - not have dependencies to services
    - SHOULD (to be handled in later versions) merging of existing `ChangeSet` instances

 - It must provide a simple `UnitOfWork` API that wraps around a `ChangeSet` and allows
    - addition of `object`s `void UnitOfWork#registerRemoved(object $object)`
    - removal of registered `object`s `void UnitOfWork#registerClean(object $object)`
    - merging of managed `objects` `void UnitOfWork#registerClean(object $object)`
    - `rollback` of current work `UnitOfWork#rollback()`
    - committing current work via `UnitOfWork#commit()`
    - simple `UnitOfWork` implementations may trigger events and use `ChangeSet` internally
    - does NOT need to be `Serializable`, since it may include dependencies