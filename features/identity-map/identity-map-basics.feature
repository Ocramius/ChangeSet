Feature: Identity Map basic functionality
  In order to store objects in the identity map
  An Identity Map should be able to fetch and retrieve objects by identities
  And Identity Map should be able to fetch and retrieve identities by object

  Background:
    Given a new IdentityMap with an IdentitySerializer

  Scenario: Register and retrieve an object
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "stdClass" and identity "123"

  Scenario: Register an object and retrieve its identity
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I can retrieve identity "123" by object "example"

  Scenario: Check for identity existence
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then identity "123" of type "stdClass" does exist in the identity map

  Scenario: Check for identity non-existence
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then identity "456" of type "stdClass" does not exist in the identity map

  Scenario: Fetch non-existing identity
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I cannot retrieve identity "456" by object "example"

  Scenario: Check for non registered objects against the identity map
    Given a new entity "example" of type "stdClass" with identity "123"
    And a new entity "another-example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "another-example" by class "stdClass" and identity "123"

  Scenario: Check for registered objects against the identity map by using a different class name
    Given a new entity "example" of type "stdClass" with identity "123"
    And a new entity "another-example" of type "anotherClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "another-example" by class "anotherClass" and identity "123"

  Scenario: Retrieve non registered objects against the identity map
    Given a new entity "example" of type "stdClass" with identity "123"
    Then I cannot retrieve object "example" by class "stdClass" and identity "123"

  Scenario: Check existence of non registered objects against the identity map
    Given a new entity "example" of type "stdClass" with identity "123"
    Then object "example" does not exist in the identity map

  Scenario: Check that objects removed from the identity map cannot be found
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    And I remove the entity "example" from the identity map
    Then I cannot retrieve object "example" by class "stdClass" and identity "123"

  Scenario: Check that objects removed from the identity map do not exist
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    And I remove the entity "example" from the identity map
    Then object "example" does not exist in the identity map

  Scenario: Check that identities of objects removed from the identity map cannot be found
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    And I remove the entity "example" from the identity map
    Then I cannot retrieve identity "123" by object "example"

  Scenario: Check that identities of objects removed from the identity map do not exist
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    And I remove the entity "example" from the identity map
    Then identity "123" of type "example" does not exist in the identity map

  Scenario: Check that objects with identities removed from the identity map cannot be found
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    And I remove the identity "123" of type "stdClass" from the identity map
    Then I cannot retrieve object "example" by class "stdClass" and identity "123"

  Scenario: Check that objects with identities removed from the identity map do not exist
    Given a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    And I remove the identity "123" of type "stdClass" from the identity map
    Then object "example" does not exist in the identity map

