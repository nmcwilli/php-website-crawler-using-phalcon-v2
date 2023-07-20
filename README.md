### Development Updates: 

Aug 25, 2021 - Fixed issue with the crontab script. It was generating incorrect crawl data for the wrong user account. 

Aug 23, 2021 - Added DOMDocument functionality to grab internal links from the url, store them in an array, then iterate through them. I also removed excess/duplicate code. The code now generates a basic HTML sitemap and stores it in the database. It also displays the sitemap html (available for download) on the front end.

Aug 21, 2021 - Added basic crawler functionality which leverages curl. Also integrated ProductsController with products/index View (volt file) so that I can properly render crawl results on view.

Aug 19, 2021 - Added form elements to submit a url to crawl on the admin side. I also create the base structure for the crawler and tested initial writes to the crawler/products table in the database. 

Aug 17, 2021 - Built base project structure and authentication system using phalcon framework. Issued first commit to private repository. 

Aug 13-16, 2021 - Reading through user story, understanding the problem and defining appropriate technology and code structure. 

### Specs Adhered to: 

#### Task 1: Build an app or plugin that delivers the desired outcome
We want you to build a PHP app or WordPress plugin that provides the desired outcome.

##### User Story
As an administrator, I want to see how my website web pages are linked together to my home page so that I can manually search for ways to improve my SEO rankings.

##### The What
- Add a back-end admin page (for WP: settings page) where the admin can log in and then manually trigger a crawl and view the results.
- When the admin triggers a crawl:
    - Set a task to run immediately.
    - Then set it to run the crawl every hour ⏰🤖.
- When the admin requests to view the results, pull the results from storage and display them on the admin page.
- If an error happens, display an error notice to inform of what happened and guide for what to do.
- On the front-end, allow a visitor to view the sitemap.html page.
- The crawl task:
    - Deletes the results from the last crawl (i.e. in temporary storage), if they exist.
    - Deletes the sitemap.html file if it exists.
    - Starts at the website’s root URL (i.e. home page)
    - Extracts all of the internal hyperlinks, i.e. results.
    - Stores results temporarily in the database.
    - Displays the results on the admin page.
    - Saves the homepage content as a static .html file, in the directory of your choice.
    - Creates a sitemap.html file that shows the results as a sitemap list structure.

##### Simplifying the Task:
Let’s keep this simple by:

- Only crawl the home webpage, i.e. instead of recursively crawling through all of the internal hyperlinks.
- Only delete the temporary stored results based on time. Normally, we would also delete them when a change in the content happens. But let’s keep it really simple and only delete based on time.
- For storage, you can use a database (MariaDB or MySQL) or the filesystem.

##### App or Plugin Expectations
- Content on the home page is dynamic.
- It lives in a GitHub repo and retains its history.
- It’s built using our assessment template.
- It’s built with modern OOP with PSR (autoloading, dependency injection, etc.)
- It uses procedural where it makes sense.
- It’s complete and works.
- It delivers the right expected outcome per what the admin requested (per the user story).
- It does not generate errors, warnings, or notices.
- It runs on the following versions of PHP: 7.0 and up.
- If built with WordPress, it runs on version 5.2 and up.
- It does not create new global variables.
- Use a MariaDB or MySQL database.

##### 🎖Extras
- Your app or plugin passes PHP Code Sniffer inspection (The PHP CS configuration file is included in this repository).
- Automatically test that your code works as expected by writing unit and integration tests. 

#### Task 2: Explain
Add an `Explanation.md` file to your repo that explains:

- The problem to be solved in your own words
- A technical spec of how you will solve it
- The technical decisions you made and why
- How the code itself works and why
- How your solution achieves the admin’s desired outcome per the user story

Think out loud. We want to know:

- How you approach a problem
- How you think about it
- Why you chose this direction
- Why this direction is a better solution

#### FAQ

##### Can I use third-party libraries or frameworks?
Yes. You are free to use to choose any third-party library or framework that suits your needs. Just make sure to explain why you choose each and how you are using them.

##### Do my git commit history matter?
Yes it does. Why? It shows us how you work.

##### Where do I ask questions?
Create an issue in your assessment repo. Let's discuss things there. Ping us. We are happy to help and collaborate.