VideoJS 6.12.1
-------------
https://github.com/videojs/video.js

Instructions to import VideoJS player into Moodle:

1. Download the latest release from https://github.com/videojs/video.js/releases
   (do not choose "Source code")
2. copy 'video.js' into 'amd/src/video-lazy.js'
3. copy 'font/' into 'fonts/' folder
4. copy 'video-js.css' into 'styles.css'
   Replace
     url("font/VideoJS.eot?#iefix")
   with
     url([[font:media_videojs|VideoJS.eot]])
   Search for other relative URLs in this file.
   Add stylelint-disable in the beginning.
   Add "Modifications of player made by Moodle" to the end of the styles file.
   Check status of:
   https://github.com/videojs/video.js/issues/2777
6. copy 'LICENSE' and 'lang/' into 'videojs/' subfolder
7. update version in thirdpartylibs.xml.

Import plugins:

As plugins are npm packaged, it is easy to download them using npm tools
run from any location inside moodle repo:

    npm install videojs-flash videojs-youtube
    +-- videojs-flash@2.1.2
    | `-- videojs-swf@5.4.2
    `-- videojs-youtube@2.6.0

Above command will download above plugins into '/node_modules' directory
located at dirroot. Now, hand-pick required files and make necessary changes.

1. Youtube plugin (https://github.com/videojs/videojs-youtube)
   Copy '/node_modules/videojs-youtube/dist/Youtube.js' into 'amd/src/Youtube-lazy.js'
   In the beginning of the js file replace
     define(['videojs']
   with
     define(['media_videojs/video-lazy']

2. Flash plugin (https://github.com/videojs/videojs-flash)
   Copy '/node_modules/videojs-flash/dist/videojs-flash.js' into 'amd/src/videojs-flash-lazy.js'
   In the beginning of the js file replace
     define(['videojs']
   with
     define(['media_videojs/video-lazy']

3. SWF plugin (https://github.com/videojs/video-js-swf)
   Copy '/node_modules/videojs-swf/dist/video-js.swf' into 'videojs/video-js.swf'

4. Reflect version changes in thirdpartylibs.xml.

As npm modules are no longer required after above steps are done, they may be deleted:
    npm remove videojs-flash videojs-youtube