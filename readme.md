# Starworld Compress

> A Wordpress plugin that lossfully (but not noticeably) compresses images uploaded to Wordpress.

## Requirements

Node needs to be installed on your server for this plugin to work.

## Setup

```
git clone
composer install
cd plugin && npm install
```

NOTE: You will likely want to run `npm install` inside a Docker container since it needs to target the OS you'll be running on (e.g. Linux).

After activating the plugin, go to the "Starworld Compress" menu to set the node path setting.

## Deploy

After setup, the `plugin/` folder will contain the plugin content. To deploy, copy the contents of the `plugin/` folder to a folder named `starworld-compress` and zip it.
