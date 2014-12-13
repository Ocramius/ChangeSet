Feature: Identity Map basic functionality
  In order to store objects in the identity map
  An Identity Map should be able to fetch and retrieve objects by identitys
  And Identity Map should be able to fetch and retrieve identitys by object

  Scenario: Register and retrieve an object
    Given a new IdentityMap with an IdentitySerializer
    And a new entity "example" of type "stdClass" with identity "123"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "stdClass" and identity "123"
    And I can retrieve identity "123" by object "example"