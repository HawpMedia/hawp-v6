# Hawp Theme v6
Bboilerplate WordPress theme for Hawp Media projects.

## Requirements
* WordPress 5.6+
* [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/)

## Basic Local Setup
1. Install the parent and child theme files. 
2. Activate the child theme. 
3. Activate ACF Pro. 
4. Navigate to Appearance > Theme Options, and save the settings.
5. The style.scss and style-editor.scss located in hawp-child > assets > sass should be compiled to hawp-child > assets > css > compiled.css and compiled-editor.css respectively. (DO NOT attempt to modify the compiled.css and compiled-editor.css files, even after launching a live site. Overrides after launch should be added to hawp-child > style.css.)

## Child Theme style.css
This stylesheet is used to override compiled styles. 

## SCSS/SASS
1. DO NOT attempt to modify the compiled.css and compiled-editor.css files, even after launching a live site. Overrides after launch should be added to hawp-child > style.css.
2. In hawp-child > assets > sass you will see several folders which include various scss files with underscores that prefix the file name. These underscores are indicating that the files are partials. These partials are then imported in the hawp-child > assets > sass style.scss and style-editor.scss files respectively. We use partials to keep the theme scss very organized. 
