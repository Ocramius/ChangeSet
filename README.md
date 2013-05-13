# ChangeSet Computer

[![Build Status](https://travis-ci.org/Ocramius/ChangeSet.png?branch=master)](https://travis-ci.org/Ocramius/ChangeSet) [![Dependency Status](https://www.versioneye.com/package/php--ocramius--proxy-manager/badge.png)](https://www.versioneye.com/package/php--ocramius--change-set) [![Coverage Status](https://coveralls.io/repos/Ocramius/ChangeSet/badge.png?branch=master)](https://coveralls.io/r/Ocramius/ChangeSet)

## Project aim

This library aims at providing abstraction for a basic changeset computer.

The idea was born from all the code duplication in the `UnitOfWork` components of the various
[doctrine projects](https://github.com/doctrine), which all require complex calculations of
changes in sets of objects over time.

With this repository, I aim at creating a simple maximum common denominator between those
[`UnitOfWork`](http://martinfowler.com/eaaCatalog/unitOfWork.html) implementations by extracting
common API and re-implementing it in a cleaner and hopefully more efficient way.

The project is a **work in progress**, so don't expect it to be usable until version `1.0.0` is tagged.

## Contributing

Please read the [CONTRIBUTING.md](https://github.com/Ocramius/ChangeSet/blob/master/CONTRIBUTING.md)
contents if you wish to help out!
