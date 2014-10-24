Feature: UnitOfWork
  In order to interact with entities
  An UnitOfWork should be able to track their state

  Scenario: Persist an entity
    Given a new UnitOfWork
    And a new object "example"
    When I persist the object "example"
    Then the object "example" must be managed by the UnitOfWork

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
