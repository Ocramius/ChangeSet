Feature: UnitOfWork keeps track of the order of operations
  In order to correctly order dependent operations
  A UnitOfWork must keep track of executed operations

  Scenario: Commit a single persisted entity
    Given a new UnitOfWork
    And a new object "example"
    And I persist the object "example"
    When I commit
    Then there is 1 object in the commit operations
    And the operation 0 is a "insert" of object "example"
