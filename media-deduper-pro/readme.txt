=== Media Deduper Pro ===
Contributors: drywallbmb, kenjigarland
Tags: media, attachments, admin, upload
Requires at least: 4.3
Tested up to: 4.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Save disk space and bring some order to the chaos of your media library by removing and preventing duplicate files.

== Description ==
Media Deduper was built to help you find and eliminate duplicate images and attachments from your WordPress media library. After installing, you'll have a new "Manage Duplicates" option in your Media section.

Before Media Deduper can identify duplicate assets, it first must build an index of all the files in your media library, which can take some time. Once that's done, however, Media Deduper automatically adds new uploads to its index, so you shouldn't have to generate the index again.

Once up and running, Media Deduper provides two key tools:

1. A page listing all of your duplicate media files. The list makes it easy to see and delete duplicate files: delete one and its twin will disappear from the list because it's then no longer a duplicate. Easy! By default, the list is sorted by file size, so you can focus on deleting the files that will free up the most space.
2. A scan of media files as they're uploaded via the admin to prevent a duplicate from being added to your Media Library. Prevents new duplicates from being introduced, automagically!

Media Deduper comes with a "Smart Delete" option that prevents a post's Featured Image from being deleted, even if that image is found to be a duplicate elsewhere on the site. If a post has a featured image that's a duplicate file, Media Deduper will re-assign that post's image to an already-in-use copy of the image before deleting the duplicate so that the post's appearance is unaffected. At this time, this feature only tracks Featured Images, and not images used in galleries, post bodies, shortcodes, meta fields, or anywhere else.

Note that duplicate identification is based on the data of the files themselves, not any titles, captions or other metadata you may have provided in the WordPress admin.

Media Deduper can differentiate between media items that are duplicates because the media files they link to have the same data and those that actually point to the same data file, which can happen if a plugin like WP Job Manager or Duplicate Post.

As with any plugin that can perform destructive operations on your database and/or files, using Media Deduper can result in permanent data loss if you're not careful. **We strongly recommend backing up your entire WordPress site before deleting duplicate media.**

== Installation ==
1. Upload the `media-deduper-pro` directory to your plugins directory (typically wp-content/plugins)
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit Media > Manage Duplicates to generate the duplicate index and see your duplicated files

== Frequently Asked Questions ==
= How are duplicates computed? =

Media Deduper Pro looks at the original file uploaded to each attachment post and computes a unique hash (using md5) for that file. Those hashes are stored as postmeta information. Once a file's hash is computed it can be compared to other files' hashes to see if their data is an exact match.

= Why does the list of duplicates include all the copies of a duplicated file and not just the extra ones? =

Because there's no way of knowing which of the duplicates is the "real" or "best" one with the preferred metadata, etc.

= Should I just select all duplicates and bulk delete permanently? =

NO! Because the list includes every copy of your duplicates, you'll likely always want to save one version, so using *Delete Permanently* to delete all of them would be very, very bad. Don't do that. You've been warned.

Instead, we recommend using the *Smart Delete* action (which is also found in the Bulk Actions menu). Smart Delete will delete the selected items one by one, and refuse to delete an item if it has no remaining duplicates. For example, if you have three copies of an image, and you select all three and choose Smart Delete, two copies will be deleted and the third will be skipped.

Again, we strongly recommend backing up your data before performing any bulk delete operations, including Smart Delete.

= Does Media Deduper Pro prevent duplicates from all methods of import? =

At this time, Media Deduper Pro only identifies and blocks duplicate media files manually uploaded via the admin dashboard -- it does not block duplicates that are imported via WP-CLI or the WordPress Importer plugin.

= I have another question! =

Check out our Media Deduper knowledge base at https://cornershop-creative.groovehq.com/knowledge_base/categories/media-deduper. If you can't find your answer there, please email us at support@cornershopcreative.com.

== Changelog ==
= 1.0.2 =
* Add a button to the Index tab that allows users to stop the indexer if it's running
* Add a setting to the License Key tab that allows users to opt in to receive beta updates
* Fix a bug that could cause the indexer to display progress incorrectly in some edge cases

= 1.0.1 =
* Fix a bug that caused the count of indexed/un-indexed items to be calculated incorrectly on some WP installs
* Calculate count of indexed/un-indexed items more frequently, to reduce the chance of inaccurate counts being displayed
* Prevent the index of post content from going out of sync if a user deactivates the plugin for a period of time and then reactivates it
* Improve behavior/language when there are no items (posts or attachments) to index at all
* Improve notices displayed to users when the index needs to be regenerated (only display to admins/privileged users, link directly to the Index tab)
* Remind users to enter license keys, so they don't miss out on updates

= 1.0.0 =
Initial public release of Media Deduper Pro. Changes compared to the free version of Media Deduper:
* Replace references in post properties and certain post meta fields (featured image, Yoast FB/Twitter images, WooCommerce product gallery)
* Perform indexing in the background, so the user doesn't have to stay on the indexer page while the process completes
* Implement license key system to allow one-click/automatic plugin updates
