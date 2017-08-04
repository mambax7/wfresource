/**
* Module: WF-Channel V2.00
* Author: Xoops CMS <webmaster@Xoops.com>
* License: GNU
*/

What is WF-Resource?

WF-Resource is the heart and soul of all the WF-Project modules. This module must be installed and
kept up to date for all the other WF-Project modules to work correctly.

Why this module?

The answer is simple and for two reasons:

1. Scalability: Rather than having to add the same functions, classes and other files over and over again within each module.
I decided that it would be easier to update and maintain if I included the most used of the above into one module. This keeps the
sizes down of all the WF-Project modules and makes development easier and quicker for myself.

2. Compatibility: I have found that over the years, Xoops (And other clones) change, and sometimes Xoops doesn't change in the area's
Module developers would like it to change. For this reason I found it prudent to create this library so I can move my modules forward
rather than relying on the core code. If something changes within Xoops that break compatibility, I just add the required code to the library.

The sad fact is, without this module I would have to release compatible modules just for 2.00 and 2.20 branches alone. I refuse to do this.
My time is limited enough and I would rather spent my time developing better quality modules than the same module for different platforms.

Fresh Install:
--------------
  1. Upload folder wfresource to xoops_root_path/modules
  2. Install the module from xoops module admin


Update from previous versions only:
--------------------------------------------
  1. Upload folder wfresource to xoops_root_path/modules overwriting your old files
  2. Install the module from xoops module admin

