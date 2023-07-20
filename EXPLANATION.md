### EXPLANATION.md

#### The problem to be solved 

Company has requested that I build a PHP app that can crawl website pages (single page), then store and retrieve/display the results to the end user. The crawler functionality must only be accessible via an admin section (authentication and user account functionality required), and it must be all built using modern OOP with PSR. 

#### Technical spec on how I will solve it

I defined the following specs below for hosting a live demo of the code and also defined the extensions and composer dependencies required for my app to run: 

Host: Amazon AWS 
Host services used: EC2, Route53, Load Balancer, EBS, Elastic IP, Certificate Manager
Server: 1 t2.micro instance to act as server (low load - for demo purposes) - resources will suffice for demo 
OS: Amazon Linux 2 
SSL/TLS capable: Yes
Webserver: Apache w/ mod_rewrite enabled
Database server: MariaDB
PHP Version: 7.4.3 
PHP Framework: Phalcon 5 alpha3 (A full-stack PHP framework delivered as a C-extension)
PHP Extensions required: Phalcon, PSR, fileinfo, curl, gettext, gd2, imagick, json, pcre-devel, pdo, openssl, mbstring, memcached, PECL (leveraged via PEAR)
PHP dependency managers: Composer
Composer dependencies: See composer.json file in repository
Package managers: PEAR, yum
PHP Template engines: Volt

For the application structure, I defined the following specs: 

- Leverage phalcon devtools to build a clean mvc project structure
- Clear separation of views and front-end elements (using volt). Folders named: about, contact, index, layouts, products, register, session and public folders with .htaccess/mod_rewrites to display appropriate view/render
- Back-end structure separated into clear folders for: Controllers, Forms, Models, Plugins and Providers

#### Technical decisions I made

After carefully reviewing the technical assessment template and user story, I decided that the best way to accomplish this, would be to leverage the Phalcon framework and build a PHP app. This would allow me to leverage a modern and fast PHP mvc framework, efficiently create an authentication system, and build the crawler functionality within that structure. 

#### How the code itself works 

Important note: You can view a live version of the code hosted here (Removed)

The code has view configured as volt files under the themes/invo/ folder. There are subfolders for pages that have multiple sub-views. For example, the core views I'm using in this project are themes/invo/products. 

The views integrate with the controllers, which can be found under the src/Controllers folder. The main controller used for this project is src/Controllers/ProductsController.php. This controller houses the main logic for the crawler, and also interfaces with the model/db. 

For my database, I am using a database created in MariaDB. The models associated with this database can be found under the src/Models directory. For example, the table in my database is called products, and this matches directly with my Models/Products.php file. The models can optionally integrate any restrictions on the data, specify the data elements being used, and also house any additional data model logic (if required). 

The "bootstrap file" can be found under public/index.php and this is what loads the environment variables, init's the Phalcon Dependency Injection, registers the service providers, and init's the MVC application to be able to send output to the client. 

The crawler functions using CURL to grab the html raw data from a url specified by the user, then uses DOMDocument to iterate through the links (grabbing their nodeValue i.e. title and url/link). I used a basic if statement to only add internal links to an array. Then we run through the unique elements of the array and generate an HTML sitemap and store it in the database (MariaDB). On the front end the user can then download a copy of the HTML sitemap. 

There is a crontab configured on the server that runs the CronController script (Removed) every 1 hour to update all of the existing crawl data for all of the users in the database. 

#### How the solution achieves the desired outcome as per the user story 

The solution achieves the desired outcome by generating a basic HTML sitemap for the user to see what links their webpage is connected to, along with their internal link titles/nodeValues. It also provides an admin section where users can register and login to, along with allowing the users to download their results via the front end. 
