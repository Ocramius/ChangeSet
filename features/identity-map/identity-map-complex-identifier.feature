Feature: Identity Map functionality with complex identifiers
  In order to store objects in the identity map
  An Identity Map should be able to fetch and retrieve objects by composite and object identities
  And Identity Map should be able to fetch and retrieve composite and object identities by object

  Scenario: Register and retrieve an object with a complex object identity
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity" of type "stdClass" and value '{"foo":"bar","baz":"tab"}'
    And a new entity "example" of type "stdClass" with the identity of "objectIdentity"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "stdClass" and the complex identity "objectIdentity"

  Scenario: Register and retrieve a complex object identity
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity" of type "stdClass" and value '{"foo":"bar","baz":"tab"}'
    And a new entity "example" of type "stdClass" with the identity of "objectIdentity"
    When I store the entity "example" in the identity map
    And I can retrieve complex identity "objectIdentity" by object "example"

  Scenario: Cannot fetch an object by non-same object identity
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity1" of type "stdClass" and value '{"foo":"bar","baz":"tab"}'
    And a new complex identity "objectIdentity2" of type "stdClass" and value '{"foo":"bar","baz":"tab"}'
    And a new entity "example" with the identity of "objectIdentity1"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "example" by class "stdClass" and the complex identity "objectIdentity2"

  Scenario: Cannot fetch an object by same object identity and different type
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity1" of type "stdClass" and value '{"foo":"bar","baz":"tab"}'
    And a new entity "example" with the identity of "objectIdentity1"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "example" by class "anotherClass" and the complex identity "objectIdentity1"

  Scenario: Register and retrieve an object by same complex array identity
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity1" of type "array" and value '{"foo":"bar","baz":"tab"}'
    And a new entity "example" of type "stdClass" with the identity of "objectIdentity1"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "stdClass" and the complex identity "objectIdentity1"

  Scenario: Register and retrieve an object by equivalent complex array identity
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity1" of type "array" and value '{"foo":"bar","baz":"tab"}'
    And a new complex identity "objectIdentity2" of type "array" and value '{"foo":"bar","baz":"tab"}'
    And a new entity "example" of type "stdClass" with the identity of "objectIdentity1"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "stdClass" and the complex identity "objectIdentity2"

  Scenario: Register and retrieve an object with complex array identity
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity1" of type "array" and value '{"foo":"bar","baz":"tab"}'
    And a new complex identity "objectIdentity2" of type "array" and value '{"foo":"bar","baz":"tab"}'
    And a new entity "example" of type "stdClass" with the identity of "objectIdentity1"
    When I store the entity "example" in the identity map
    Then I can retrieve object "example" by class "stdClass" and the complex identity "objectIdentity1"
    Then I can retrieve object "example" by class "stdClass" and the complex identity "objectIdentity2"
    And I can retrieve complex identity "objectIdentity1" by object "example"

  Scenario: Register and fail to retrieve an object with non-equivalent complex array identity
    Given a new IdentityMap with an IdentitySerializer
    And a new complex identity "objectIdentity1" of type "array" and value '{"foo":"bar","baz":"tab"}'
    And a new complex identity "objectIdentity2" of type "array" and value '{"foo":"bar"}'
    And a new entity "example" of type "stdClass" with the identity of "objectIdentity1"
    When I store the entity "example" in the identity map
    Then I cannot retrieve object "example" by class "BaseEntity" and the complex identity "objectIdentity2"
