Feature:
  In order to prove that Comic API works
  As a user
  I want to have a API import comic scenario

  Background:
    Given I am authenticated as "admin@mo2o.com"

  Scenario: It receives a response with a list of comics
    Given I send a "PATCH" request to "/api/comics/import"
    And the response status code should be 200
    Then 185 async messages must be scheduled



