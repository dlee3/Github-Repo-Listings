# PHP Repo App
This is a one page app that displays the top 100 PHP repos on Github.

I decided to limit this to the top 100 for simplicity sake but it could easily
be expanded to show more.

It will automatically update the repo listings every 15 minutes.




## Setup
Automatic Setup:
* Input your root/admin DB credentials into the config.ini file located in the base folder.
    - Input to info under the [dev:production] heading near the bottom.
    - The DB will be automatically created so just name it whatever you'd like it to be.
* If you want to setup a virtual host, point the web root to public_html.
* In your browser, navigate to the installApp.php file in the web root to run the install.
  For instance, if you have a host of example.com, go to `example.com/installApp.php`
* After the script runs successfully, you need to either move the installApp.php file out
  of the web root or delete it for security reasons.
* Now you can use the app.


If for some reason the Auto Setup doesn't work, you'll need to do a manual setup:
* Input your root/admin DB credentials into the config.ini file located in the base folder.
    - Input to info under the [dev:production] heading near the bottom.
* Create a database with the same name you entered into the config.ini file.
  Example MySQL statement: `CREATE DATABASE $dbName`
* Create the tables. Here's sql to do that:
    
    CREATE TABLE `config` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `item` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `amount` int(22) DEFAULT NULL,
          `updated` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    
    CREATE TABLE `repos` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `repo_id` int(22) unsigned NOT NULL,
          `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `stars` int(22) DEFAULT NULL,
          `pushed` datetime DEFAULT NULL,
          `created` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
* Run the composer install from terminal/command line.
  Statement to run: `php composer.phar install`
* If you want to setup a virtual host, point the web root to public_html.
* If you haven't already, you need to either move the installApp.php file out
  of the web root or delete it for security reasons.
* Now you can use the app.



## Usage
* Navigate to the web root (located at public_html).
* On a large screen:
    - On the left you will see a sortable list.
    - On the right you will see the details of the current repo.
* On a small screen:
    - The sortable list will be on the top.
    - The details of the current repo will be on the bottom.
* On a large screen - if you opt to have enough repos displayed
  that you need to scroll, the Repo Details will stick to the top of the screen.
* The columns in the Repo list are sortable by clicking the column headers.


## Notes
* Architecture:
    - The gathering and storage of the repos is done in the Github Model.
    - The page rendering is handled by the IndexController.
    - The Templates folder houses all of the views.
    - The main SCSS file is the site-wide.scss file in the base folder/
        - This file uses the mixins and site files located in the scss folder
          to create the site-wide.css file.
    - The CSS and JS files are located in their respective
      folders in the public_html directory.

* Explanations:
    - Normally I would not include the environment.inc in the git repository
      but since this is for presentation purposes I have included it for
      simplicity sake.
    - I used Slim because of simplicity and familiarity. This is not the most
      current version of Slim but I used this version because it's was I used
      at my last job so I knew I could build it fast.
    - I thought about making this a multi-page app or using AJAX to get and
      display repo information but in the end I decided that would have just been
      excess. Yes, it would have showcased my skills but it wouldn't have been
      as user-friendly.
    - There are no tests included because with such a small app I didn't feel it was necessary.
    - An extra security measure that could have been added would have
      been to escape the Github output when it was displayed on the screen.
    - If I had more time:
        - I would have added logging for when the Github API is accessed and the
          repo info is saved.
        - I would build out the interface some more. Like adding more repo info,
          more views, and more fancy stuff.


