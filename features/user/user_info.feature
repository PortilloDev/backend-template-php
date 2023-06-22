Feature:
  In order to prove that user info API works
  As a user
  I want to have a API user scenario

  Background:
    Given I am authenticated as "admin@mo2o.com"

  Scenario: It receives a response with a list of comics
    When I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should exist

