# Upgrade guide

## Version 3.0

Released: TBD

Breaking changes:
- PHP version required is now 8.3, (dropped support for PHP 8.2)
- PHP 8.5 is also supported

## Version 2.0

Released: 2024-08-24

Breaking changes:
- PHP version required is now 8.2, PHP 8.3 is also supported
- Types added to all code base, also strict types are used
- All the SIE\Data classes are now final, and vars private instead of protected
- As object is now a reserved name in PHP the Data\Object class is now renamed to DimensionObject instead

Tooling added:
- Docker compose file added to ease local development, with envs for each supported PHP version
- ECS coding standard added, also integrated with github action to auto fix committed issues to PRs
- Rector added for automatic refactors and to keep code standard modern, also integrated in github actions
- Phpstan added and checked automatically in Github actions, solved all existing issues
- Phpunit added with the simple test example as a test case (more to be added)
- Github dependabot for upgrades of dev dependencies
- Github actions for running unit tests on supported PHP versions

The are all wrapped in composer scripts and you can run them this way:
```bash
composer cs-check
composer cs-fix
composer rector-check
composer rector-fix
composer phpunit
```

## Version 1.0

Released: 2015-09-21

Initial version