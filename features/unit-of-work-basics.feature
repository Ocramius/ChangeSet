Feature: UnitOfWork
  In order to interact with entities
  An UnitOfWork should be able to track their state

  Scenario: Persist an entity
    Given a new UnitOfWork
    And a new object "example"
    When I persist object "example"
    Then the object "example" must be managed by the UnitOfWork

