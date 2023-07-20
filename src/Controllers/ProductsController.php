<?php

declare(strict_types=1);

namespace Invo\Controllers;

use Invo\Forms\ProductsForm;
use Invo\Models\Products;
use Invo\Models\Users;
use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model\Query;


/**
 * ProductsController (Main Crawler Controller)
 *
 * This contains the primary code for the Crawler
 * Created by Neil McWilliam
 * 
 */

class ProductsController extends ControllerBase
{
    // Set public variables
    public $url;
    public $user_id;

    /**
     * Products initializer
    */
    public function initialize()
    {
        $this->tag->setTitle('Manage and View your Crawls');

        parent::initialize();
    }

    /**
     * Main crawler page (authenticated)
     */
    public function indexAction(): void
    {
        // Get users session info
        $auth = $this->session->get('auth');

        // Define the user variable
        $user = Users::findFirst($auth['id']);

        // If no auth, send them to index
        if (!$user) {

            // Dispatch them to the index
            $this->dispatcher->forward([
                'controller' => 'index',
                'action'     => 'index',
            ]);

            return;
        }

        // Set the user_id
        $user_id = $auth['id']; 

        // No Post - set the following vars
        if (!$this->request->isPost()) {

            // Detect if they have a URL crawler setup
            $urlCount = Products::findFirst(['id = ?0', 'bind' => [$user_id]]);
            $urlCount = count($urlCount);

            // If no crawler setup, then indicate message
            if ($urlCount == 0 || $urlCount == NULL){

                // Flash a message that they must configure a url to see crawling information
                $this->flash->notice('You must provide a URL to receive crawl information.');

            // If there is a crawler setup, then display default URL
            } else {

                // Grab the crawl results
                $productsSearch = Products::findFirst(['id = ?0', 'bind' => [$user_id]]);

                // Render error if one occurs
                if ($productsSearch == 0 || $urlCount == NULL){

                    foreach ($productsSearch->getMessages() as $message) {
                        $this->flash->error((string) $message);
                    }

                // Otherwise show results
                } else {

                    // Set the url for the field on the view
                    $this->tag->setDefault('url', $productsSearch->url);

                    // Set the crawl data variables for the view 
                    $this->view->url = $productsSearch->url; 
                    $this->view->last_update = $productsSearch->last_update; 
                    $this->tag->setDefault('html', $productsSearch->html); 
                    $this->tag->setDefault('sitemap', $productsSearch->sitemap); 
                    
                } 
            }

        // Post submitted - Update the database with their new crawl information
        } else {

            // Set crawl time and memory limits 
            set_time_limit (1209600);
            ini_set('memory_limit', '-1');

            // Grab the submitted URL
            $url = $this->request->getPost('url', 'string');

            // No recursion or depth used, because we are just searching the url they submitted
            // I thought about 'forcing' the domain, but figured I would leave it as url for greater flexibility with the tool
            $parse = parse_url($url); 
            $domain = $parse['host']; 

            // Detect url length 
            $urlLength = strlen($url); 

            // Define url with extra http://, https://, http://www. and https://www. -- Just to see 
            $urlWithHttp = "http://".$url;
            $urlLengthWithHttp = strlen($urlWithHttp);
            $urlWithHttps = "https://".$url;
            $urlLengthWithHttps = strlen($urlWithHttps);
            $urlWithWWW = "http://www.".$url;
            $urlLengthWithWWW = strlen($urlWithWWW);
            $urlWithWWWs = "https://www.".$url;
            $urlLengthWithWWWs = strlen($urlWithWWWs);

            // Detect if they have a URL crawler setup
            $urlCount = Products::findFirst(['id = ?0', 'bind' => [$user_id]]);
            $urlCount = count($urlCount);

            // If they DO already have a url/crawl setup, delete existing data 
            if ($urlCount >= 1 && $urlCount != NULL){

                // Delete the results, sitemap and html content from the last crawl (i.e. in temporary storage), if they exist.
                $ids = [$user_id];
                $records = Products::find([
                    'conditions' => 'id IN (:ids:)',
                    'bind' => array('ids' => implode(', ', $ids)),
                ]);
                $records->delete();   
            } 

            // Get the HTML SOURCE CODE using curl
            $html = curl_init($url);
            curl_setopt($html, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)');
            curl_setopt($html, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($html, CURLOPT_BINARYTRANSFER, true);
            $contentHtml = curl_exec($html);
            curl_close($html);

            // If we have no html data, render a message instead
            if ($contentHtml=="" || $contentHtml==NULL){
                $contentHtml = "No html data was able to be pulled.";
            } 

$contentSitemapStart = "
<!doctype html>
<html>
<body>

<h1>Your HTML Sitemap</h1>
<p>This is a basic HTML sitemap that you can download and customize!</p>

<h2>Page Links for: ".$url."</h2>

<ul>";

            // Grab live page html content
            $myhtml = <<<EOF
            $contentHtml
EOF;

            // Create a new DOM Document to hold page structure
            // Hide HTML warnings
            libxml_use_internal_errors(true);
            $doc = new \DOMDocument();
            $doc->loadHTML($myhtml);

            $tags = $doc->getElementsByTagName('a');

            // Create an empty array to hold the results
            $array = array(); 

            // Store each value in an array
            foreach ($tags as $tag) { 
                    if (
                    $tag->getAttribute('href') == "/" || 
                    substr($tag->getAttribute('href'), 0, 1) == "/" || 
                    substr($tag->getAttribute('href'), 0, 1) == "#" || 
                    substr($tag->getAttribute('href'), 0, $urlLength) == $url || 
                    substr($tag->getAttribute('href'), 0, $urlLengthWithWWW) == $urlWithWWW || 
                    substr($tag->getAttribute('href'), 0, $urlLengthWithWWWs) == $urlWithWWWs || 
                    substr($tag->getAttribute('href'), 0, $urlLengthWithHttp) == $urlWithHttp || 
                    substr($tag->getAttribute('href'), 0, $urlLengthWithHttps) == $urlWithHttps
                ){
                    // Create the link with title and add it to the array
                    // $array[] = "<a href='".$tag->getAttribute('href')."'>".$tag->getAttribute('title')."</a>";

                    $array[] = array(
                        'href' => $tag->getAttribute('href'),
                        'title' => trim($tag->nodeValue)
                    );
                } 
            } 

            // Store unique array elements
            // $uniqueArray = array_unique($array);
            $uniqueArray = array_unique($array,SORT_REGULAR);

            // Loop through the unique links and add them to the contentSitemapMiddle part
            // foreach ($uniqueArray as $internalLink) {
            foreach($uniqueArray as $key => $value){

                // Add internal links to the html content to build the sitemap 
                $contentSitemapMiddle = 
$contentSitemapMiddle."
  <li><a href='".$value['href']."'>".$value['title']."</a></li>"; 
            } 

            // end of content
            $contentSitemapEnd = "
</ul>  

</body>
</html>
";

            // Finalize contentSitemap html
            $contentSitemap = $contentSitemapStart.$contentSitemapMiddle.$contentSitemapEnd;

            // Stores results in the database
            // Create the new url entry in the table
            $products = new Products();
            $products->id = $user_id; // The user's ID 
            $products->url = $url; // Set the url
            $products->last_update = new RawValue('now()'); // track the current date/time
            $products->html = $contentHtml; // Save the full html page 
            $products->sitemap = $contentSitemap; // Save it as a sitemap.html file 
            $products->active = '1'; 

            // Displays the results on the admin page.

            // Render error if one occurs
            if (!$products->save()) {

                foreach ($products->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }

            // Otherwise show results
            } else {

                // Render success message
                $this->flash->success('We have successfully updated your URL.');

                // Set the url for the field on the view
                $this->tag->setDefault('url', $products->url);

                // Set the crawl data variables for the view 
                $this->view->url = $products->url; 
                $this->view->last_update = $products->last_update; 
                $this->tag->setDefault('html', $products->html); 
                $this->tag->setDefault('sitemap', $products->sitemap); 
                
            } 
        }
    }

    /**
     * Edit the active user profile (authenticated)
     */
    public function profileAction(): void
    {
        // Get users session info
        $auth = $this->session->get('auth');

        // Define their ID
        $user = Users::findFirst($auth['id']);

        // If no auth, send them to index
        if (!$user) {

            // Dispatch them to the index
            $this->dispatcher->forward([
                'controller' => 'index',
                'action'     => 'index',
            ]);

            return;
        }

        // No post - Set email and name
        if (!$this->request->isPost()) {

            $this->tag->setDefault('name', $user->name);
            $this->tag->setDefault('email', $user->email);

        // Post submitted - Update their information
        } else {

            // Grab name and email from post vars
            $user->name = $this->request->getPost('name', ['string', 'striptags']);
            $user->email = $this->request->getPost('email', 'email');

            // If no auth user can be detected, throw error
            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }

            // Flash success message, if everything worked properly
            } else {
                $this->flash->success('Your profile information was updated successfully!');
            }
        }
    }
}
