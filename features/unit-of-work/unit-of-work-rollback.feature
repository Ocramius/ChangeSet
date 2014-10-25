Feature: UnitOfWork Rollback
  In order to rollback a transaction
  An UnitOfWork should be able to restore object status

  Scenario: Revert a single entity
    Given a new UnitOfWork
    And a new object "example"
    When I register the object "example"
    And I change the object "example"
    And I rollback
    Then the object "example" should return to its original state

  Scenario: Revert a single entity
    Given a new UnitOfWork
    And a new object "example"
    When I change the object "example"
    And I register the object "example"
    And I rollback
    Then the object "example" should be in its changed state
