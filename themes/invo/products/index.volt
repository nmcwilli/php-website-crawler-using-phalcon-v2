<h3>Your Crawler Setup:</h3>

<p>This tool will crawl whatever url you submit, <u>without recursion</u>. It will also refresh your crawl data <u>every 1 hour</u>.</p>

<form action="/products/index" role="form" method="post" id="profileForm">
    <div class="form-group">
        <label for="url">Submit a URL:</label>
        {{ text_field("url", "size": "2083", "class": "form-control", "placeholder": "Enter URL here with http:// or https:// prefix") }}
    </div>

    <input type="submit" value="Update" class="btn btn-primary btn-large btn-info">
</form>

<hr>

<h3>Your Crawl Results:</h3>

<p>
Last crawl date/time: <br>
{{ last_update }}
</p>

<p>
URL we crawled: <br>
<a href='{{ url(url) }}' target="_blank">
    {{ url }}
</a>
</p>

<p>
HTML Sitemap Generated: <br>
<form role="form" id="sitemapData">
    <div class="form-group">
        {{ text_area("sitemap", "class": "form-control", "rows": "8") }}
    </div>
</form>
</p>

<p>
HTML raw data: <br>
<form role="form" id="htmlData">
    <div class="form-group">
        {{ text_area("html", "class": "form-control", "rows": "8") }}
    </div>
</form>
</p>