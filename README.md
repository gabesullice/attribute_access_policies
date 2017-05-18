# Attribute-based Access Policies

Attribute-based Access Policies is special implementation of a Policy plugin as defined by the [Entity Access Policies module](https://github.com/gabesullice/entity_access_policies) module.

Attribute policies are written in YAML.

## Overview

In english, attribute policies read like this: "If a user has a certain field value, and an entity has a certain value, the user should be able to do X, Y or Z to the entity."

In YAML, they read like this:

```yaml
id: 'first_letter_policy' # Arbitrary, unique name.
entity_types: ['taxonomy_term'] # Can be a list of any entity types.
operations: ['view', 'delete'] # Can be a list of one or many of: 'view', 'update', 'delete'
entity_condition: # The condition to evaluate for the entity
  members:
  - type: condition
    property: 'name.0.value' # The field path. These can traverse entity references!
    operator: 'STARTS_WITH' # See available operators below.
    comparison: 'a'
user_condition: # The condition to evaluate for the user
  members:
  - type: condition
    property: 'name.0.value'
    operator: 'STARTS_WITH'
    comparison: 'B'
```

The above policy would let any user with a username starting with the character `B`, _view_ or _delete_ any taxonomy term whose name begins with the character `a`.

## Let's get complicated

You can go _craaazy_ with your conditions. There are two types of conditions that can go under the `member` key. Those are: `condition` and `condition_group`.

`condition_group`
- `conjunction`
  - Allowed values are `AND` or `OR`. The default is `AND`.
- `members`
  - Allowed values are just more nested `condition` and `condition_group`s. The default is just an empty list.

`condition_group`
- `property`
  - The value of the entity to evaluate. You can think of this like a property selector. You can get deeply nested values by chaining fields together. You can read more about the path syntax below.
- `operator`
  - Allowed values are: `=` `<>` `<` `<=` `>` `>=` `CONTAINS` `IN` `NOT IN` `STARTS_WITH` `ENDS_WITH` `BETWEEN` `NOT BETWEEN`. The default is `=`.
- `comparison`
  - This is the value that you want to compare against. E.g. `10` or `'foo'`

**Property Paths**

Property paths can follow the field names and properties of those paths. They can collect values from multi-value fields and can even traverse entity references.

The syntax is simple, just concatenate your field names, property names, and indices with dots `.`.

Example: `uid.0.name.value`

Remember that all fields in Drupal actually are _multi-value_ fields. So if you omit an index, you're going to get a **list** of values. Not a single value. Choose your operator accordingly. You can't compare a single value with a list. This would **not work** `5 = [1, 1, 2, 3, 5]`. However, this **would work** : `5 IN [1, 1, 2, 3, 5]`.
