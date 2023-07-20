<?php

declare(strict_types=1);

namespace Invo\Controllers;

use Invo\Forms\ProductsForm;
use Invo\Models\Products;
use Invo\Models\Users;
use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model\Query;

/**
 * CronController (Main Cron Script)
 *
 * This script is run via cronjob and updates the crawl data in the database every 1 hour
 * Created by Neil McWilliam
 * 
 */

class CronController extends ControllerBase
{
    // Set public variables
    public $url;
    public $user_id;

    /**
     * Products initializer
    */
    public function initialize()
    {
        $this->tag->setTitle('Cronjob that updates DB every hour');

        parent::initialize();
    }

    /**
     * Main crawler cronjob script 
     */
    public function indexAction(): void
    {
        
        // Set crawl time and memory limits 
        set_time_limit (1209600);
        ini_set('memory_limit', '-1');

        // Grab the crawl data
        $productsSearch = Products::find();

        // If there is at least 1 crawl, then update it. 
        if ($productsSearch->count() >= 1){

            // Loop through and refresh the results 
            foreach ($productsSearch as $productSearch) {

                // echo $product->url, PHP_EOL;
                // echo $product->last_update, PHP_EOL;
                // echo $product->id, PHP_EOL;
                // echo $product->html, PHP_EOL;
                // echo $product->sitemap, PHP_EOL;
                // echo $product->active, PHP_EOL;

                // Set the content sitemapmiddle to empty
                $contentSitemapMiddle = ""; 

                // Create an empty array to hold the results
                $array = array(); 

                // Define the user variable
                $user_id = $productSearch->id;

                // Grab the submitted URL
                $url = $productSearch->url;

                // No recursion or depth used, because we are just searching the url they submitted
                // I thought about 'forcing' the domain, but figured I would leave it as url for greater flexibility with the tool
                $parse = parse_url($url); 
                $domain = $parse['host']; // detect the domain 

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

                // Confirm they have a URL crawler setup (to be paranoid!)
                $urlCount = Products::findFirst(['id = ?0', 'bind' => [$user_id]]);
                $urlCount = count($urlCount); 

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

                // Stores results in the database
                // Create the new url entry in the table
                $products = new Products();
                $products->id = $user_id; // The user's ID 
                $products->url = $url; // Set the url
                $products->last_update = new RawValue('now()'); // Track the current date/time
                $products->html = $contentHtml; // Save the full html page 
                $products->sitemap = $contentSitemap; // Save it as a sitemap.html file 
                $products->active = '1'; 

                // Render error if one occurs
                if (!$products->save()) {

                    foreach ($products->getMessages() as $message) {
                        $this->flash->error((string) $message);
                    }

                // Otherwise show results
                } else {

                    // Render success message
                    $this->flash->success('We have successfully updated the crawl.');                    
                } 
            }
        }
    }
}
