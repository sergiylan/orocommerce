Feature: CRUD operations for Coupons codes
  As an Administrator
  I want to be able to CRUD Coupons codes via Management Console UI,
  So we need to add Management Console UI for CRUD operations with coupon codes

  Scenario: Create coupon
    Given I login as administrator
    And go to Marketing/Promotion/Coupons
    And I click "Create Coupon"
    When I fill form with:
      |Coupon Code       |12345    |
      |Uses per Coupon   |1        |
      |Uses per Customer |1        |
    And I save and close form
    Then I should see "Coupon has been saved" flash message
    And I should see "12345" in grid with following data:
      |Uses per Coupon   |1        |
      |Uses per Customer |1        |
      |Used              |0        |

  Scenario: View existing coupon
    Given I go to Marketing/Promotion/Coupons
    And I click view "12345" in grid
    Then I should see coupon with:
      |Coupon Code       |12345    |
      |Uses per Coupon   |1        |
      |Uses per Customer |1        |

  Scenario: Edit existing coupon
    Given I go to Marketing/Promotion/Coupons
    And click edit "12345" in grid
    And fill form with:
      |Coupon Code       |12345    |
      |Uses per Coupon   |10       |
      |Uses per Customer |1        |
    When I save and close form
    Then I should see "Coupon has been saved" flash message
    And I should see "12345" in grid with following data:
      |Uses per Coupon   |10       |
      |Uses per Customer |1        |

  Scenario: View edited coupon
    Given click view "12345" in grid
    Then I should see coupon with:
      |Code              |12345    |
      |Uses per Coupon   |10       |
      |Uses per Customer |1        |

  Scenario: Create second coupon
    Given I go to Marketing/Promotion/Coupons
    And I click "Create Coupon"
    And I fill form with:
      |Code              |54321    |
      |Uses per Coupon   |10       |
      |Uses per Customer |10       |
    When I save and close form
    Then I should see "Coupon has been saved" flash message
    And I should see "54321" in grid with following data:
      |Uses per Coupon   |10       |
      |Uses per Customer |10       |
      |Used              |0        |
    And I should see "12345" in grid with following data:
      |Uses per Coupon   |10       |
      |Uses per Customer |1        |
      |Used              |0        |

  Scenario: Delete existing coupon
    Given I click delete "12345" in grid
    And I click "Yes, Delete" in modal window
    Then I should see "Coupon deleted" flash message
    And I should see "54321" in grid with following data:
      |Uses per Coupon   |10       |
      |Uses per Customer |10       |
      |Used              |0        |
    And I should not see "12345"

  Scenario: Create coupon with existing code
    Given I go to Marketing/Promotion/Coupons
    And I click "Create Coupon"
    And I fill form with:
      |Code               |54321    |
      |Uses per Coupon    |15       |
      |Uses per Customer  |10       |
    When I save and close form
    Then I should see validation errors:
      |Coupon Code |This value is already used. |

  Scenario: Create coupon with empty 'uses per' fields
    Given I go to Marketing/Promotion/Coupons
    And I click "Create Coupon"
    And I fill form with:
      |Coupon Code       |22222    |
      |Uses per Coupon   |         |
      |Uses per Customer |         |
    When I save and close form
    Then I should see "Coupon has been saved" flash message
    And I should see "22222" in grid with following data:
      |Uses per Coupon   |         |
      |Uses per Customer |         |

  Scenario: Save coupon without required data
    Given I go to Marketing/Promotion/Coupons
    And I click "Create Coupon"
    When I save and close form
    Then I should see validation errors:
      |Coupon Code |This value should not be blank. |
