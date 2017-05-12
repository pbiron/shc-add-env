# Add Environment to Admin Bar
Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Dev, Local, etc)

## Description
If you're like me, you often have multiple versions of the same WordPress site open in different browser windows, e.g.,  production in one window and development in another window.  And if you're like me, you have unwittingly edited content on the development environment thinking you were doing so in the production environment.  If so, then this plugin is for you!

This plugin adds an indication of the current environment to the Admin Bar that is easier to see than your browser's address bar.

![Production](screenshots/prod.png?raw=true "Production")
![Development](screenshots/dev.png?raw=true "Development")
![Local](screenshots/local.png?raw=true "Local")

## Installation
Installation of this plugin works like any other plugin out there. Either:

1. Upload the zip file to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

### Minimum Requirements
* WordPress 3.1 or greater

## Filters

By default, this plugin knows about 3 different environments:

* Local &mdash; if the IP address of the server is a loopback (e.g., 127.0.0.1) or a LAN (e.g., 192.168.1.1) or the host name is 'localhost'
* Dev &mdash; if WP_DEBUG is defined and true
* Prod &mdash; everything else

However, you can define your own environments by hooking into the ``shc_add_env_get_env`` filter, such as:

```PHP
add_filter ('shc_add_env_get_env', 'my_env') ;

function
my_env ($env)
{
	if (false !== stripos ($_SERVER['HTTP_HOST'], 'qa.')) {
		// if the HTTP_HOST is something qa.example.com
		return ('QA') ;
		}

	return ($env) ;
}
```

When hooking into ``shc_add_env_get_env`` to define your own environments, you should always also enqueue a stylesheet that defines style(s) for those environments:

```PHP
add_action ('init', 'my_env_init') ;

function
my_env_init ()
{
	if (is_admin ()) {
		add_action ('admin_enqueue_scripts', 'my_env_enqueue') ;
		}
	else if (is_admin_bar_showing ()) {
		add_action ('wp_enqueue_scripts', 'my_env_enqueue') ;
		}

	return ;
}

function
my_env_enqueue ()
{
	wp_enqueue_style ('my_env_show_env', plugins_url ('css/my-env-show-env.css', __FILE__)) ;
	
	return ;
}
```

where ``css/my-env-show-env.css`` would contain a rule such as:

```CSS
#wpadminbar .ab-top-menu .shc-add-env.qa .ab-item,
#wpadminbar .ab-top-menu .shc-add-env.qa:hover .ab-item
{
	background-color: #523f6d ;
}
```

Care should be taken to select colors that have sufficient contrast to the background-color of the admin bar for each of the Admin Color Scheme's shipped with WordPress.  If sufficient contrast can't be guaranteed, then add additional styling to help the Admin Bar stand out, e.g.,

```CSS
.admin-color-sunrise #wpadminbar .ab-top-menu .shc-add-env.prod .ab-item
{
	border: 1px solid black ;
	box-sizing: border-box ;
}
```

## Ideas?
Please let me know by creating a new [issue](https://github.com/pbiron/shc-add-env/issues/new) and describe your idea.  
Pull Requests are welcome!

## Inspriation

I was inspired to write this plugin when I saw the [Blue Admin Bar](https://wordpress.org/plugins/blue-admin-bar/) plugin.  I thought that was a great idea, but having the background-color of the entire Admin Bar be different was a bit jarring.