<div class="row">
    <div class="col-md-6">
        <div class="page-header">
            <h2>Log In</h2>
        </div>

        <p style='color:red;'>Note: This is a demo app and you can login with <b>demo/demo</b></p>

        <form action="/session/start" role="form" method="post">
            <fieldset>
                <div class="form-group">
                    <label for="email">Username/Email</label>
                    <div class="controls">
                        {{ text_field('email', 'class': "form-control") }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="controls">
                        {{ password_field('password', 'class': "form-control") }}
                    </div>
                </div>
                <div class="form-group">
                    {{ submit_button('Login', 'class': 'btn btn-primary btn-large') }}
                </div>
            </fieldset>
        </form>
    </div>

    <div class="col-md-6">
        <div class="page-header">
            <h2>Don't have an account yet?</h2>
        </div>

        <p>Create an account to get the following:</p>
        <ul>
            <li>Access to crawl your website</li>
            <li>Learn valuable SEO insight about your website</li>
            <li>Setup auto-crawlers that run every 1 hour</li>
        </ul>

        <div class="clearfix center">
            {{ link_to('register', 'Sign Up', 'class': 'btn btn-primary btn-large btn-success') }}
        </div>
    </div>
</div>
