CHANGELOG
==========================

## 1.1.0 Alpha 1 (`1010011`)

- **New:** Seed for media gallery category (#7)
- **Change:** Set minimum Faker version to 1.9 (#3)
- **Change:** Require each seed to have its own CLI command to allow better configuration (#5)

## 1.0.1 (`1000170`)

- **New**: Set current time in XF after every seed data being inserted
- **New**: Clear entity cache after every seed
- **New**: Conversation and conversation message seeds
- **New**: Allow specifying limit when running only a specific seed
- **Changed**: Users will now watch their own threads when seeding
- **Changed**: Updated Faker to be compatible with PHP 7.4
- **Fixed**: When seeding pages `node_name` will be set
- **Fixed**: Unable to seed without escaping class name

**Note:** You can now remove both `composer.json` and `composer.lock` after installing the add-on from release build.

## 1.0.0 (`1000070`)

- **Changed**: Rewritten the job
- **Changed**: Separated tck-seeder:seed command into two CLI commands
- **Fixed**: Do not throw exception if seed handler does not exist

## 1.0.0 Alpha 3 (`1000013`)

- **New**: Added new class `AbstractReactionContent` to allow reacting to random contents
- **Changed**: Now makes use of code even listener instead of content type
- **Changed**: Changed the option from `content-type` to `seed` for the CLI command

## 1.0.0 Alpha 2 (`1000012`)

- **New**: Set tags when creating threads
- **New**: Users will now have an avatar
- **New**: User will also choose to accept/deny admin emails
- **Changed**: Users will be more unique
- **Changed**: Reorganize the way next seed is calculated
- **Fixed**: Wrong job filename
- **Fixed**: Limit being set to between 1K and 5K instead of 5K and 10K for Thread seed
- **Fixed**: Argument 1 passed must be of the type array or null, string is given when running seeder for a specific content type

## 1.0.0 Alpha 1 (`1000011`)

First alpha release.