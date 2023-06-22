Feature:
    In order to prove that Comic API works
    As a user
    I want to have a API comic scenario

    Background:
        Given I am authenticated as "admin@mo2o.com"

    Scenario: It receives a response with a list of comics
        When I send a "GET" request to "/api/comics"
        Then the response status code should be 200
        Then the response should be in JSON


    Scenario: It receives a response with a list of comics with params
        When I send a "GET" request to "/api/comics?publisher=marvel"
        Then the response status code should be 200
        Then the response should be in JSON


    Scenario: I want to create a new comic
        Given I add "Content-Type" header equal to "application/json"
        When I send a "POST" request to "/api/comics" with body:
        """
        {
            "title": "o2o Avengers",
            "publisher": "o2o"
        }
        """
        Then the response status code should be 200
        And I consume messages
        Then I send a "GET" request to "/api/comics"


    Scenario: I want to validate comic fields
        Given I add "Content-Type" header equal to "application/json"
        When I send a "POST" request to "/api/comics" with body:
        """
        {}
        """
        Then the response status code should be 400
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And the JSON should be equal to:
        """
          {
              "errors": {
                  "title": [
                      "This value should not be blank."
                  ],
                  "publisher": [
                      "This value should not be blank."
                  ]
              }
          }
        """