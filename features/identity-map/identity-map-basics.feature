Feature: Identity Map basic functionality
  In order to store objects in the identity map
  An Identity Map should be able to fetch and retrieve objects by identifiers
  And Identity Map should be able to fetch and retrieve identifiers by object

  Scenario: Register and retrieve an object
    Given a new IdentityMap with an IdentifierSerializer
    And a new entity "example" with identifier "123"
    When I store the entity "example" in the
    Then I can retrieve object "example" by identifier "123"
    And I can retrieve identifier "123" by object "example"
