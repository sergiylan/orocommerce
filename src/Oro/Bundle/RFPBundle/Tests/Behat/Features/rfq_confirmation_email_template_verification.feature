@fixture-OroProductBundle:product_frontend_single_unit_mode.yml
Feature: RFQ confirmation email template verification
  In order to check that the html comments are correct worked in the template and are edited through the WYSIWYG editor
  As a Administrator
  I am changing the email template with WYSIWYG editor

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | User  | second_session |
    And I proceed as the Admin
    And login as administrator
    And I go to System/Configuration
    And I follow "Commerce/Sales/Request For Quote" on configuration sidebar
    And uncheck "Use default" for "Enable Guest RFQ" field
    And I check "Enable Guest RFQ"
    And I save setting
    And I follow "Commerce/Sales/Shopping List" on configuration sidebar
    And uncheck "Use default" for "Enable guest shopping list" field
    And I check "Enable guest shopping list"
    And I save setting
    And I should see "Configuration saved" flash message

#  Scenario: Changing the email template with WYSIWYG editor
#    Given I go to System / Emails / Templates
#    And I filter Template Name as is equal to "request_create_confirmation"
#    And I click "edit" on first row in grid
#    When I submit form
#    Then I should see "Template saved" flash message

  Scenario: Verified email template source
    Given I proceed as the User
    And I am on the homepage
    And type "Product" in "search"
    And I click "Search Button"
    And I click "Add to Shopping List"
    And I click "Shopping list"
    And click "Request Quote"
    And I fill form with:
      | First Name    | John                  |
      | Last Name     | Testerson             |
      | Email Address | testerson@example.com |
      | Phone Number  | 72 669 62 82          |
      | Company       | Red Fox Tavern        |
      | PO Number     | PO Test 01            |
    When I click "Submit Request"
    Then I should see "Request has been saved" flash message
    And Email should contains the following "PSKU1" text
