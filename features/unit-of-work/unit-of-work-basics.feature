Feature: UnitOfWork Object States
  In order to interact with entities
  An UnitOfWork should be able to track their state

  Scenario: Persist an entity
    Given a new UnitOfWork
    And a new object "example"
    When I persist the object "example"
    Then the object "example" must be managed by the UnitOfWork
    And the object "example" must be marked as "new"

  Scenario: Persist and immediately remove an entity
    Given a new UnitOfWork
    And a new object "example"
    When I persist the object "example"
    And I remove the object "example"
    Then the object "example" must be unknown to the UnitOfWork

  Scenario: Persist multiple entities
    Given a new UnitOfWork
    And a new object "example1"
    And a new object "example2"
    When I persist the object "example1"
    And I persist the object "example2"
    Then the object "example1" must be managed by the UnitOfWork
    And the object "example2" must be managed by the UnitOfWork
    And the object "example1" must be marked as "new"
    And the object "example1" must be marked as "new"

  Scenario: Persist and immediately remove multiple entities
    Given a new UnitOfWork
    And a new object "example1"
    And a new object "example2"
    When I persist the object "example1"
    And I persist the object "example2"
    And I remove the object "example1"
    And I remove the object "example2"
    Then the object "example1" must be unknown to the UnitOfWork
    And the object "example2" must be unknown to the UnitOfWork

  Scenario: Register an entity
    Given a new UnitOfWork
    And a new object "example"
    When I register the object "example"
    Then the object "example" must be managed by the UnitOfWork

  Scenario: Register and immediately remove an entity
    Given a new UnitOfWork
    And a new object "example"
    When I register the object "example"
    And I remove the object "example"
    Then the object "example" must be managed by the UnitOfWork
    And the object "example" must be marked as "removed"

  Scenario: Register multiple entities
    Given a new UnitOfWork
    And a new object "example1"
    And a new object "example2"
    When I register the object "example1"
    And I register the object "example2"
    Then the object "example1" must be managed by the UnitOfWork
    And the object "example2" must be managed by the UnitOfWork
    And the object "example1" must be marked as "managed"
    And the object "example2" must be marked as "managed"

  Scenario: Remove an un-managed entity
    Given a new UnitOfWork
    And a new object "example"
    Then I cannot remove the object "example"

  Scenario: Remove and immediately persist an entity
    Given a new UnitOfWork
    And a new object "example"
    When I register the object "example"
    And I remove the object "example"
    And I persist the object "example"
    And the object "example" must be managed by the UnitOfWork
    And the object "example" must be marked as "managed"

  Scenario: Remove and immediately manage an entity
    Given a new UnitOfWork
    And a new object "example"
    When I register the object "example"
    And I remove the object "example"
    And I register the object "example"
    And the object "example" must be managed by the UnitOfWork
    And the object "example" must be marked as "managed"


