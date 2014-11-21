#Installation

## Requirements

  * Ruby 1.9+
  * [Node.js](http://nodejs.org)
  * [compass](http://compass-style.org/): `gem install compass`
  * [bower](http://bower.io): `npm install bower -g`

## Quickstart

  * Clone the repository to a directory with your new project name.
  * Upload the directory to wp-content/themes.
  
Then when you're working on your project, just run the following command:

```bash
bundle exec compass watch
```

This will watch for changes to your .scss files and compile them according to the settings found in config.rb.

Once everything is compiled, upload the style.css file.

Hit
```bash
Control + C
```
to stop watching for changes.

## Upgrading

If you'd like to upgrade to a newer version of Foundation down the road just run:

```bash
bower update
```
