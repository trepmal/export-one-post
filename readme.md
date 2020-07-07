# Export One Post

Works on pages and custom post types!

In the Block Editor, the option can be found under the *kebab* menu:

![In the Block Editor](readme-assets/block-editor.png)

In the Classic Editor, the option can be found in the Publish box:

![In the Classic Editor](readme-assets/classic-editor.png)

And in the row actions for quick access:

![Row actioln](readme-assets/row-action.png)


Confirmed working as of WordPress 5.4.1, single and multisite. 

## WP-CLI

**There is a core command, this plugin is *not* required.**

```
wp export --post__in=123
```