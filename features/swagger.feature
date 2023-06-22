Feature:
  In order to document the application
  As a user
  I want to check the swagger is well formatted

  Scenario: OpenAPI UI is enabled for docs endpoint
    Given I add "Accept" header equal to "text/html"
    And I send a "GET" request to "/api/doc"
    Then the response status code should be 200
    And the header "Content-Type" should be equal to "text/html; charset=UTF-8"
    And I should see text matching "My App"

  Scenario: OpenAPI extension properties is enabled in JSON docs
    Given I send a "GET" request to "/api/doc.json"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "openapi" should be equal to "3.0.0"
    And the JSON node "info.title" should be equal to "My App"
