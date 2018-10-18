=== CodeShop Amazon Affiliate ===
Contributors: codeapple
Tags: amazon advertise products, amazon affiliate, shop, store, ecommerce, wordpress with amazon, amazon products API, amazon associate, monetize posts
Donate link: https://codeapple.net/
Requires PHP: 5.3.0
Requires at least: 4.4
Tested up to: 4.8
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

CodeShop Amazon Affiliate plugin to setup a complete amazon shop solution. Simple & fast, also monetize your Wordpress posts.

== Description ==

* Convert your wordpress into complete amazon affiliate store
* Sell amazon products and earn $$$
* Monetize your wordpress regular posts / pages by advertising amazon products
* Customizable products display templates to make it work with any theme
* Create product categories and add products using your prefer search option, add unlimited products into categories
* Set Amazon Shop page as front page to display all your added products
* Products display with standard pagination
* All pages have breacrumb for easy navigation
* Widget ready to show all added product categories into your sidebar
* All available features and how to use them you will find detail on plugin [documentation](https://codeapple.net/codeshop-amazon-affiliate/documentation/)

Create a complete amazon shop of thousands product by creating different product categories and add products accordingly. 
Also support adding products with Wordpress regular posts and pages. Make ready your site as you want with customizable 
templates to advertise amazon products to sell and earn commissions. So monetize your Wordpress websites with simple &
easy CodeShop Amazon Affiliate plugin.

Front end products display templates are customizable, you can just copy them into your active theme directory and customize
templates as you want to make it look same with your active theme. A lot of product attributes are available to display into your
customized templates. So use product attributes what you want to show to users.

You can add product categories and add products to them by searching on amazon through amazon products API. Plugin will
use amazon advertising API at backend to search products, you just need to put your search text / keyword or ASINs and click the
Search button to get your results and select them all or few of them which products you prefer to add to set up your Amazon
Shop using your Wordpress site.

You can create your own product categories. Product categories will be shown as breadcrumb into pages for easy navigation 
and products will have standard pagination to show, products display per page is easy to change through front end templates.

'Amazon shop categories' widget is ready to display all your added categories ( if products are already added into them ) with your sidebar.

'Amazon Shop' page will be created on activation of the plugin and you can just use Settings -> Reading -> Set 'Amazon Shop' page 
as your front page from the dropdown list, your homepage will be shown now all your added products with pagination. Default to 
display 12 products per page with four columns view of products. You can change products display columns view with how many 
products will be shown on each page. you just need to copy the whole folder 'amazonshop-templates' from plugin directory into
your active theme directory, and you are free to customize your templates including stylesheets.


== Installation ==

**Minimum Requirements**

* WordPress 4.4 or higher
* PHP version 5.3.0 or higher
* MySQL version 5.0 or higher
* PHP SOAP extension enabled

**Automatic installation**

Automatic installation is the easiest option as WordPress handles file transfers itself and you don't need to leave your web browser. 
To do an automatic install of plugin, log in to your WordPress admin dashboard, navigate to the Plugins menu and click Add New.
In plugin search field type 'Codeshop Amazon Affiliate' and click Search Plugins. Once you've found plugin you can install it by simply 
clicking 'Install Now'.


**Manual installation**

The manual installation method involves downloading CodeShop Amazon Affiliate plugin and uploading it to your webserver via your favourite 
FTP application. WordPress codex has detail instructions how to install plugin manually.
 [Click to see detail](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation )

== Frequently Asked Questions ==

= What should I do first after activate the plugin? =
First you need to add your API credentials to work amazon advertising API basically which works when you will search
amazon products and will add them with your posts. Go to menu CodeShop -> Settings page to set your API
credentials.

= What should I do next after putting all credentials to make it work? =
Well, when you put your all credentials into CodeShop -> Settings page and hit the save button then you should see
'Test Settings' button, better click on that to be sure your API credentials working okay, If you see success message after 
a while when you click 'Test Settings' button then you ready to go set up your shop, if you see failed mesage then you need 
to double check your API credentials properly. REMEMBER if you are testing it on your localhost then make sure your internet
connectivity is working okay else it will be failed to connect with Amazon advertising API servers.

= How to access plugin detail documentation? =
CodeShop Amazon Affiliate Plugin website has detail 
[documentation](https://codeapple.net/codeshop-amazon-affiliate/documentation/) which will make easier to use the plugin.

= How to ask about plugin related issues or get support? =
You can ask on CodeShop Amazon Affiliate Plugin website 
[forum](https://codeapple.net/codeshop-amazon-affiliate/forum/) to ask anything about plugin related issues. You can also
ask on wordpress [support forum](https://wordpress.org/support/plugin/codeshop-amazon-affiliate).


= How will I add products? =
Using menu CodeShop -> Add New products you can add products by searching by text / keywords or by entering
ASINs ( Amazon Standard Identification Numbers ). When you have search results you can select all
products or can select which ones you want to add, then click on 'Add Selected Products' to add your all selected products.
You should see success message when products are added and then hit the publish button and visit the post page to see your
added products. 

If you want to add products with your wordpress regular posts / pages then when you are in Posts -> Add New or
Pages -> Add New area then you should see 'CodeShop' button appeared above text editor at the same line where
you have your 'Add Media' button. Click on 'CodeShop' button and you should see modal window through where you
can search by text / keywords, when you have search results then you can select products which ones you want to show
with your posts and then click 'Add Shortcode' button, shortcode will be created automatically for you and will be added
in your post editor, then just hit the publish button and visit the post page to see your added products.

REMEMBER you can change all kind of front end products display as you want to show it by customizing templates.

= How will I edit products? =
Using menu 'CodeShop -> All Products' you see all added posts. Just click on edit post as like regular wordpress
posts you do edit, then you will see all your added products with the post in your edit screen and then you can remove them by 
selecting checkbox or add more by searching as your requirements.

= How to display all added product categories into Sidebar? =
In menu Appearance -> Widgets you should see 'Amazon Shop categories' widget , just drag and drop to your
sidebar area where you want to show all added product categories. REMEMBER if you added categories and don't
have posts yet into those categories then those categories will not be shown. So add posts into your added 
categories to see them into your sidebar. So categories with no post will not be shown.

= How can I update or modify one column / two columns / three columns / four columns product view templates? =
Well, still you didn't copy the whole folder 'amazonshop-templates' from plugin directory then just copy the folder into
your active theme directory, for better understanding after copy the folder in your active theme the new 'amazonshop-templates'
folder path should be as like wp-content/themes/{your-active-theme}/amazonshop-templates/, now you are safe to update or
modify templates, where you should see all customizable templates. 

Suppose, If you want to update / modify two columns product view template then just open the template file 'product-two-columns.php' and 
update it as you want to make it work and corresponding stylesheet for this template file will be found at same folder as 
'assets/css/product-two-columns.css' to add / update your stylesheet. Same way for other templates -

* template file 'product-one-column.php' will use the stylesheet 'assets/css/product-one-column.css'  
* template file 'product-three-columns.php' will use the stylesheet 'assets/css/product-three-columns.css' 
* template file 'product-four-columns.php' will use the stylesheet 'assets/css/product-four-columns.css' 

so one template file including its stylesheet update / modify will not affect others.

= So am I limited to display products only with one column or two columns or three columns or four columns view? =
Basically, you are not limited! Templates are added as standard so you can update them, suppose you want to
show more than four columns product view by updating template file 'product-four-columns.php' then you can just 
update / modify contents to show as 5 columns or 6 columns or whatever you prefer you can do but just don't change 
the template file name 'product-four-columns.php'  and its corresponding styesheet 'assets/css/product-four-columns.css'. 
So you can display products as you want just keeping the template file names unchanged.

== Screenshots ==

1. After activate the plugin you should see Admin CodeShop main menu and submenus as displayed in image screenshot-1.png
2. CodeShop -> Settings page required all credentials to enter properly to work the plugin as displayed in image screenshot-2.png
3. After provided all credentials when you click Save Settings button then you should see Test Settings button to test your amazon credentials working okay or not as like displayed in image screenshot-3.png
4. When you click Test Settings button then you should see success message if everything works fine as like displayed in image screenshot-4.png
5. When you want to add products using CodeShop -> Add New Products menu then you should see the search options by keyword or ASINs to add products below your post editor as like displayed in image screenshot-5.png
6. As am example if you put any keyword to search and click on search button then searching products message should be shown as like displayed in image screenshot-6.png
7. When search results will be returned from amazon then you should see results with option to select / de select all products with button Add Selected Products to add selected products with your post as like displayed in image screenshot-7.png
8. When search results will be returned from amazon then you should see results with pages so you can see more results on different pages by click on page numbers and also can add them with your post, so you can add more than 10 products at a time for same search results as like displayed in image screenshot-8.png
9. Using menu CodeShop -> Categories you can create your product categories / sub-categories as you want to set up your shop categories structures, sample categories are shown in image screenshot-9.png
10. Using Wordpress menu Settings -> Reading you can set your default auto created amazon shop page as front page to display all your added products  as homepage screenshot-10.png
11. Using Wordpress menu Appearance -> Widgets you can drag & drop amazon shop categories widget to show all your added categories in your theme Sidebar screenshot-11.png
12. When you are in Wordpress menu Posts -> Add New or Pages -> Add New area then you should see CodeShop button above the text editor as display in image to add amazon products with your Wordpress regular posts / pages screenshot-12.png
13. When you click on CodeShop button then you should have modal window to search products and create Shortcode with your selected products to add with your posts / pages, you can also select display templates shown in image screenshot-13.png
14. Your auto created Shortcode should be added as like image when you click Add Shortcode button from modal window screenshot-14.png
15. Front end display products as four columns view showing products results count including Sidebar added widget showing product all categories, shown as sample in image screenshot-15.png
16. Front end display products as two columns view showing pagination shown as sample in image screenshot-16.png
17. Showing four columns product view with different theme shown as sample in image screenshot-17.jpg
18. Showing four columns full width product view with different theme shown as sample in image screenshot-18.jpg
19. Showing four columns product view with different theme shown as sample in image screenshot-19.jpg
20. Showing three columns product view shown as sample in image screenshot-20.jpg
21. Showing four columns product view with different theme shown as sample in image screenshot-21.jpg
22. Showing two columns product view shown as sample in image screenshot-22.jpg
23. Showing two columns product view with different theme shown as sample in image screenshot-23.jpg
24. Showing single / one column product view shown as sample in image screenshot-24.jpg

== Changelog ==

= 2.0.0 [ 2017 - 09 -05 ] =
* Added# New submenu 'Display Options'
* Added# Search products on all amazon available product categories like Appliances, Clothing, Health, Movies, Books, Baby, Toys etc.
* Added# Sort search product results on all amazon available sort parameters like sales, review, popularity, price etc.
* Added# Add all available search results products for different categories
* Added# Set different products display templates through easy admin panel drop down menu selection
* Added# Set different product templates display image sizes ( Small / Medium / Large ) through easy admin panel selection
* Added# Set all product title length through admin panel
* Added# Change default AMAZON BUY button through admin panel
* Added# Set products cache system duration to peform / load faster your website than earlier
* Added# Show / Hide last updated checked time of products price information to users through admin panel


= 1.1.2 [ 2017 - 03 -18 ] =
* Updated# Display products offer price instead of lowest new price as amazon also shows products offer price

= 1.1.1 [ 2017 - 03 -13 ] =
* Fixed# codeshop button modal window search results

= 1.1.0 [ 2017 - 03 -13 ] =
* Added#  More than 10 products can be added on same search keyword results
* Added#  Search results will show all available pages for 'All' Amazon Product Category


= 1.0.0 [ 2017 - 02 -11 ] =
* Initial released.

== Upgrade Notice ==

= 1.0.0 =
This is initial released version, new features will be added gradually so do upgrade when you see new update version available in your Plugins admin panel.
