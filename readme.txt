=== InTrigger - Conversion & Lead Generation ===
Contributors: InTrigger

Tags: lead generation, lead gen, email generation, lead gen plugin, lead generation plugin, wordpress lead generation, optin form, email list, optinmonster, subscribers plugin, optin plugin, floating bar, floating bar plugin, hellobar, hellobar alternative, bottom bar, custom bar, inline widget, wordpress pop up, wordpress optin form, after post optin form, middle post, hide post, 

Requires at least: 3.5.1
Tested up to: 4.4
Stable tag: trunk
License: GNU General Public License v2.0 or later

Create scenarios to display inline forms, read more boxes or floating bars with advanced targeting rules.

== Description ==
Convert active visitors into subscribers, downloads or ad revenues with smart scenarios. InTrigger plugin allows you to insert inline forms or floating bar depending on pages keywords and user behavior.
 
= Scenario #1 - Inline forms or banners =
InTrigger plugin allows you to insert within some targeted posts the widget you want: optin form, message driving traffic to another page, custom banner, etc.
Because visitors are used to banners in sidebars and top bars, we have to find new types of marketing tools to increase lead generation’s performance, and inline optin forms are one of the most effective way to collect more emails.

Inline scenarios are really powerful when used with our keyword targeting tool. 

= Example Scenario #2 - Continue Reading =
When a visitor that has already read 2 posts, starts reading a 3rd one, he is most likely interested by your content. 
Thanks to our "Read more" scenario, you can hide part of the 3rd post content, and invite visitors to subscribe or click in order to display the full post. 

The result is amazing. Media websites using this scenario were able to increase their number of subscribers by more than 800%, without annoying people that are just curious and don't read full posts.

= Example Scenario #3 - Floating bar =
The floating bar on the top of your screen has become a standard tool of a effective lead generation strategy thanks to HelloBar. 
Unfortunately, Hellobar is a paid service and its targeting settings are poor.

Creating a "Floating bar" scenario allows you to display a pre-made or custom bar at the top or bottom of your screen.
Most importantly, you will be able to display the floating bar when and where it is relevant. For example, if a visitor reads 2 posts related to "Topic A", and then scrolls 50% of a third post, he shows true engagement signs, it is the perfect time to display a bottom floating bar (optin form, message, ad, etc.) as he is scrolling.



= For all scenarios - Advanced targeting =
Whatever the scenario, you have numerous options to target specific visitors segments and apply the scenario within specific conditions:
<ul>
<li>Pages / Posts / CPT / URLs / Keywords targeting</li>
<li>Time spent on the website</li>
<li>Number of pages visited</li>
<li>Logged / Not logged users</li>
<li>Desktop, Tablet or Mobile device</li>
<li>Marketing pressure: Max number of impressions per session, stop scenario after conversion, etc.</li>
</ul>

= For all scenarios - Indget customization =
When you create a scenario, you choose to display a specific "Indget" under certain conditions. The indget could be either a default form or an easily customizable message box: text, background color, border color, font-size, etc. If you have some basic HTML / CSS skills, you can also create your own custom indget.


= Full Features List =
* Scenario 1 - Inline post: Insert a form or message in the middle of a selection of posts
* Scenario 2 - Continue reading: Hide post content after x% and invite the user to subscribe or click to display full post
* Scenario 3 - Floating bar: Display a floating bar at the top / bottom of screen after some scroll
* 6 default indgets (forms & messages) with easy customization: background color, font-size, etc.
* Powerful targeting rules: pages selection by URL or keywords, number of pages visited, device, etc.
* Shortcodes to apply a scenario or display an indget on a specific template / page
* Performance statistics (impressions, conversion rate,..) at scenario and indget level
* Export your contacts (CSV) & synchronize them with SendinBlue (and soon other email marketing providers)
* Advanced settings: contacts dedupe, Search Engine bots exclusion (continue scenario), etc.

= Credits =
This plugin is created by the <a href="http://www.intriggerapp.com" rel="friend" title="InTrigger">InTrigger</a> team based in Paris.


== Installation ==

1. Install InTrigger plugin either via the WordPress.org plugin repository or by uploading the files to your server.
2. Activate InTrigger plugin from the Plugins tab - Installed plugins.
3. Navigate to the InTrigger tab at the bottom of your admin menu and follow instructions in the homepage to create your first scenario

== Frequently Asked Questions ==

= How does it work? =

The plugin allows you to create scenarios that display an indget (form, message, ..) when some conditions are met. For example, thanks to "Continue" scenario, you can display an email form inviting readers to subscribe for your newsletter in order to read full post.

= Will scenarios make my site load slower? =

No. When we add content to your site, we are doing it via JavaScript after the page loads. This means that all of your primary content will be loaded first and InTrigger will be triggered shortly after the page actually loads.

= Can I use an indget without a scenario? =

Yes. To display an indget without including it in a scenario, you can use the shortcode [intrigger indget="XXX"] where XXX is the indget ID. You will find the ID and more details about the shortcode at the bottom of the indget "Edit" page.


= Can I apply several scenarios on the same page? =

Yes, but only with different scenarios types. For example, you can apply a "Floating Bar" scenario and an "Inline" scenario on the same page, but you cannot apply two "Inline" scenarios. When you apply several scenarios with the same type on a page, only the lowest id scenario will apply.

= Can I use an indget without a scenario? =

There are two ways for customizing indgets. You can either create a custom indget and insert your own HTML / CSS, or use a template indget and apply your changes to the appropriate CSS class. For example, if you want to modify the template indget "Inline - Collect" with the default theme, you should use the class "int_indget_inline_collect_default".

= Is the plugin really free? =

Yes, InTrigger plugin is free, and will always remain free. We might offer a paid-version with some extra features, but all main features will always remain free.

= I can't find a way to do X... =

The plugin is actively developed. If you can't find your favorite feature, or if you have any suggestion, please contact us. We would love to hear from you.


== Screenshots ==
1. First, your are invited to create your fist scenario and choose between the first 3 scenario types
2. Let's take the example of the Continue Reading scenario. We are gonna hide 70% of post content when user has already read 3 posts and invite the user to subscribe if he wants to read full posts.
3. Each scenario displays an indget, which can be an optin form, a message with a button, or whatever you want.
4. You can easily customize the default indgets, or create your own custom indget.
5. Use targeting rules to apply scenarios only on some pages for specific behaviors.
6. For each scenario (and indget), you have all useful stats: impressions, conversions, and conversion rate.
7. All subscribers generated from forms are saved in a specific contact section with automatic deduplication
8. You can synchronyze your contacts with SendinBlue, and soon with the main email marketing platforms.


== Changelog ==
= 1.0.1 =
* Fix shortcode issue
= 1.0.2 =
* Fix statistics issue in Scenarios / Indgets pages
= 1.0.3 =
* add Pages excluded rules
= 1.0.4 =
* fix duplicated jQuery function.
= 1.0.5 =
* fix deduped contacts issue.
= 1.0.6 =
* fix sendinblue account issue.