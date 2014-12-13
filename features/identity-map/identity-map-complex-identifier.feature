Feature: Identity Map functionality with complex identifiers
  In order to store objects in the identity map
  An Identity Map should be able to fetch and retrieve objects by composite and object identities
  And Identity Map should be able to fetch and retrieve composite and object identities by object

  Scenario: Register and retrieve an object
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity" of type "stdClass" and value '{"foo":"bar","baz":"tab"}'
    And a new entity "example" of type "stdClass" with the identity of "objectIdentity"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "stdClass" and the complex identity "objectIdentity"
    And I can retrieve complex identity "objectIdentity" by object "example"

  Scenario: Check for non registered objects
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity1" of type "stdClass" and value '{"foo":"bar","baz":"tab"}'
    And a new complex identity "objectIdentity2" of type "stdClass" and value '{"foo":"bar","baz":"tab"}'
    And a new complex identity "objectIdentity3" of type "stdClass" and value '{"baz":"tab"}'
    And a new entity "example" with the identity of "objectIdentity1"
    And a new entity "anotherExample1" of type "BaseEntity" with the identity of "objectIdentity1"
    And a new entity "anotherExample2" with the identity of "objectIdentity2"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "example" by class "BaseEntity" and the complex identity "objectIdentity1"
    Then I cannot retrieve object "example" by class "stdClass" and the complex identity "objectIdentity2"
    Then I cannot retrieve object "example" by class "stdClass" and the complex identity "objectIdentity3"

# @TODO add scenarios for fetching identifiers and for bulk API calls
