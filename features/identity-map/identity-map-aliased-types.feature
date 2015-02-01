# This feature is required in order to correctly handle aliased object types, such as when proxies are involved
Feature: Identity Map inheritance identifiers
  In order to store equivalent objects in the identity map
  An Identity Map should be able to fetch and retrieve objects by identities even though the class name does not match

  Background:
    Given two aliased types "type1" and "type2"
    And a new IdentityMap with an IdentitySerializer

  Scenario: Register and retrieve an object by aliased type
    Given a new entity "example" of type "type1" with identity "123"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "type2" and identity "123"

  Scenario: Register and retrieve an object by inverse aliased type
    Given a new entity "example" of type "type2" with identity "123"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "type1" and identity "123"

