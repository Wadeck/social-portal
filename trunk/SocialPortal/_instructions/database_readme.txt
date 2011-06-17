To be able to connect to the database, you need to have:

- MySql Server
- InnoDb installed on it (not the engine by default but installed by default on wampserver 2.1)
- create a database called 'social_portal'
- use collation 'utf8_general_ci
- create a user for this database login:doctrine_user pass:doctrine_s3cr3t

- then run the file scripts/create_database.php

- once everything else is done, you can execute the url /tool
	and then create forum
	and finally create user (order doesn't matter)
	
Here you have the database set up with default forum, admin, anon and nullUser ! Congratz