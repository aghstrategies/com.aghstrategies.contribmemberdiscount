Feature: Discount for Members on Contribution Page

Scenario: A logged in member goes to a contribution Page

When on a contribution page
And that contribution page is saved as a page to apply a discount on
And the user is logged in
And the user has a membership of a type that qualifies (settings page)
And a membership status that qualifies
Then a discount is applied of the amount set on the settings page


Scenario: An anonymous user goes to a contribution Page

When on a contribution page
And that contribution page is saved as a page to apply a discount on
And the user is not logged in
Then a message is displayed at the top of the page that says members of x type get a discount please login if you are a member to get the discount.

Scenario: A logged in member goes to a contribution Page

When on a contribution page
And that contribution page is saved as a page to apply a discount on
And the user is logged in
And the user does not have a membership of a type that qualifies (settings page)
Then a message is displayaed that says members receive a discount become a member!
