Feature: Map entities between orm and odm
    In order to have associations between orm entities and odm documents
    I should be able to map associations between entities and documents
    and I should be able to map references between documents and entities

    Background:
        Given I have a oneToMany association between entity "myEntityClass" and document "myDocumentClass"
          And the association is mapped by "myDocuments"
          And the association is configured to cascade "persist"
          And I associate "MyEntity" of type "myEntityClass" to "MyDocument1, MyDocument2" documents of type "myDocumentClass"

    Scenario: Get the associated documents
         When I iterate through the property "myDocuments" of "MyEntity"
         Then I should get "MyDocument1, MyDocument2"

    Scenario: Cascade persist documents
         When I persist and flush "MyEntity"
         Then "MyDocument1, MyDocument2" should be persisted

    Scenario: Check association is persisted correctly
          And I persist and flush "MyEntity"
          And I close the manager
         When I retrieve "MyEntity" from the repository
          And I iterate through the property "myDocuments" of "MyEntity"
         Then I should get "MyDocument1, MyDocument2"