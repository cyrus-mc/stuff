Requirements
************
    PHP 4+
    GD2
    Apache webserver (or another PHP enabled webserver)

Installation
*************

1. Untar the source package to document root dir or your webserver
	- cd DOCUMENT_ROOT
	- tar xvfz iFoto-VERSION.tar.gz

2. Change 'data' folder permission so that webserver has write access in it
	- chmod 755 data/

3. Create your gallery in 'gallery/' folder

4. Upload your photos inside the folder you created inside 'gallery/'

You can try download and look the sample in 'iFoto-0.20.tar.gz'


Important Notes
****************

When you have a lot of photos in a directory, it takes a while to generate all the thumbnails. The script may timeout (usually after 30 second). You just refresh you browser to continue generate the rest of the thumbnails. The script only needs to generate thumbnail once.




Thanks!

...
Aizu Ikmal Ahmad

http://www.ikmal.com.my
aizu@ikmal.com.my