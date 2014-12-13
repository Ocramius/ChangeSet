Feature: Identity Map basic functionality
  In order to store objects in the identity map
  An Identity Map should be able to fetch and retrieve objects by identities
  And Identity Map should be able to fetch and retrieve identities by object

  Scenario: Register and retrieve an object
    Given a new IdentityMap with an IdentitySerializer
    And a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "stdClass" and identity "123"

  Scenario: Register an object and retrieve its identity
    Given a new IdentityMap with an IdentitySerializer
    And a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    And I can retrieve identity "123" by object "example"

  Scenario: Check for non registered objects against the identity map
    Given a new IdentityMap with an IdentitySerializer
    And a new entity "example" of type "stdClass" with identity "123"
    And a new entity "another-example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "another-example" by class "stdClass" and identity "123"

  Scenario: Check for registered objects against the identity map by using a different class name
    Given a new IdentityMap with an IdentitySerializer
    And a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "another-example" by class "anotherClass" and identity "123"

  Scenario: Check for non-existing identity and correct class name
    Given a new IdentityMap with an IdentitySerializer
    And a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    And I cannot retrieve object "example" by class "stdClass" and identity "456"
