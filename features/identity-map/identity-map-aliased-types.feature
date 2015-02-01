# This feature is required in order to correctly handle aliased object types, such as when proxies are involved
Feature: Identity Map inheritance identifiers
  In order to store equivalent objects in the identity map
  An Identity Map should be able to fetch and retrieve objects by identities even though the class name does not match

  Background:
    Given a subtype "type2" for type "type1"
    And a new IdentityMap with an IdentitySerializer

  Scenario: Register and retrieve an object by its subtype will fail
    Given a new entity "example" of type "type1" with identity "123"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "example" by class "type2" and identity "123"

  Scenario: Register and retrieve an object by its supertype
    Given a new entity "example" of type "type2" with identity "123"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "type1" and identity "123"

  Scenario: Register and remove an object will make it unavailable also by subtype
    Given a new entity "example" of type "type2" with identity "123"
    When I store the entity "example" in the identity map
    When I remove the entity "example" from the identity map
    Then I cannot retrieve object "example" by class "type1" and identity "123"
