CHANGELOG
==========================

## 1.1.0 Release Candidate 1 (`1010051`)

- **New:** Seed for thread prefix (#50)
- **New:** Seed for resource prefix (#51)
- **New:** Seed for thread prefix group (#52)
- **New:** Seed for resource prefix group (#53)
- **Change:** Switch to `fakerphp/faker` package (#54)

## 1.1.0 Alpha 4 (`1010014`)

- **New:** Seed for threadmark category (#37)
- **New:** Seed for post threadmark (#38)
- **New:** Seed for conversation message threadmark (#39)
- **New:** Seed for discouraged IP addresses (#41)
- **New:** Seed for banned users (#43)
- **New:** Seed for IP address bans (#44)
- **New:** Seed for rejected users (#42)
- **New:** Seed for server error logs (#45)
- **New:** Seed for spam trigger log (#46)
- **New:** Seed for spam cleaner log (#47)
- **New:** Seed for email bounce log (#48)
- **Fix:** Threads cannot be seeded (#36)
- **Fix:** Master template is not created for page nodes (#49)

## 1.1.0 Alpha 3 (`1010013`)

- **Fix:** Updated license and readme files are not copied correctly to add-on archives (#29)
- **Fix:** Unused content type fields still exist even after not being used at all anymore (#30)
- **Fix:** Argument 2 must be an instance of ForumEntity, instance of Finder given exception is thrown when seeding thread (#31)

## 1.1.0 Alpha 2 (`1010012`)

- **New:** Seed for media gallery comment (#12)
- **New:** Seed for media gallery item reaction (#13)
- **New:** Seed for media gallery comment reaction (#14)
- **New:** Seed for media gallery album reaction (#15)
- **New:** Seed for profile post (#16)
- **New:** Seed for profile post comment (#17)
- **New:** Seed for profile post reaction (#18)
- **New:** Seed for profile post comment reaction (#19)

## 1.1.0 Alpha 1 (`1010011`)

- **New:** Seed for media gallery category (#7)
- **New:** Seed for resource manager category (#6)
- **New:** Seed for media gallery item (#4)
- **New:** Seed for resource manager item (#8)
- **New:** Seed for resource manager update (#9)
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