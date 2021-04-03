# fields
## Form - 1
- user_id         [ref]
- license_key
- license_type    [ref]
## Form - 2
- license_key



# forms
1. create form
    - generate license_key for user 
    - show user info
2. submission form
    - submit
    - show message [success|error]



# table
- users         [ok]    -mr
- licenses      [--]    -mc
- license_types [--]    -m


--------------------------------
# Table Details
--------------------------------
## users
- first_name
- last_name
- organization
- street
- city
- email
- mobile_number
- password
- license_key
- expire_date
## table: licenses
- id
- license_key
- expire_date

## table: licenses_types
- id
- type
--------------------------------