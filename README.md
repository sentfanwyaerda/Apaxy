Apaxy++
=======
<strong>Apaxy++</strong> is a fork of [Apaxy](https://github.com/AdamWhitcroft/Apaxy), written by [adamwhitcroft](http://www.adamwhitcroft.com/). A good point of reference is his [README.md](./README-Apaxy.md).

I only took his work, mixed it with available tools like [CKeditor](http://ckeditor.com/) and [Michelf's php-markdown](https://github.com/michelf/php-markdown/) (the first plus), and a set of handwritten php and javascripts ([the second plus](./plus-plus/)) to accompany them to the point I would my Apaxy to be.

###Features:
<em>each of them is experimental!!</em>

- Displaying the content of ``README.md`` in the header
- Editing of ``.md`` files, with the authentication of your linux box
- <em>(alpha)</em> showing history diff's of files within a GiT repository
- Validation of archives through ``.md5`` postfixed files, and clean-up of these files

###Installation:
On my server ``/.theme/`` is a symbolic link to [apaxy/theme/](apaxy/theme/). Likewise ``/.tools/ckeditor`` links to the CKeditor directory.
