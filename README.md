# datamapps

Setup
=====

1. Create a database on the machine you're deploying to (or a db you are using to test with)
1. Tweak db.php config and db config in ORMApp/Services.php
1. Make a proxies directory in the root of the project and make it writeable.
1. Run ./oconsole orm:generate-proxies
1. Run ./oconsole migrations:migrate

Running The Tests
=================
1. `./oconsole test:ormapp create`
1. `./mconsole test:mapperapp create`
1. `./oconsole test:ormapp read`
1. `./mconsole test:mapperapp read`

Cleaning out data
=================
1. `./oconsole test:ormapp delete`

