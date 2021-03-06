Hug Community Web Application (www.hugcommunity.org)
=============================
A project of Carver County Community Social Services (http://www.co.carver.mn.us/departments/CSS/).

In partnership with the Hug Me Hug YOU! Initiative (www.hugmehugyou.org).

PROJECT
-------
Abused children and children in foster care interact with a plush toy-like therapeutic companion that sends and receives messages.
A support community or safety network made up of family, friends, and social workers interact with a web or mobile interface to
invite and register community members, receive updates on the child's status, preview and send messages to the child, and view analytics
to better understand and work with both the child and the community.

There will be at least 1 therapeutic companion for a Carver County Social Services initially for the Alpha and Beta Releases;
in addition, we expect to deliver between 8 and 50 for Launch.  We believe that for each therapeutic companion there will be approximately
5-10 members of a community receiving and sending messages to a child. 

Finally, our plan is to partially or fully fund this project with Kickstarter and expect to offer similar therapeutic companions as toys in
order to raise the amount necessary to make a production run within 6 months of the start of the Kickstarter project.  We expect to have
upwards of 500 additional therapeutic companions made if this is the case.  This would mean there would be upwards of 5000 community members
receiving and sending messages.

For the future, other children will be added from the same county, other children from other counties, and funds continued to be raised by
using the donations for a toy approach.  Our expectation is that within 2 years to have 2000 therapeutic companions in use and upwards of
20,000 community members receiving and sending messages.

FEATURES
--------
* Login & Authentication for Admins, Social Workers, and Community Members
* An Admin can Invite Social Workers to build a Safety Team of Community Members to Support a Child who has been given a Therapeutic Companion to Interact with
* Social Workers can Invite Community Members to join a Child's Safety Team
* The Safety Team can view and respond to critical and non-critical data related to a Child's Interaction with the Therapeutic Companion
* The Safety Team can listen to the same audio the Child hears from their Interaction with the Therapeutic Companion and then preview and send audio messages to be played back for the Child by the Therapeutic Companion

GET STARTED
-----------
The project is built using PHP, CodeIgniter (http://ellislab.com/codeigniter), and Ion Auth (https://github.com/benedmunds/CodeIgniter-Ion-Auth).  In addition to this repository, a development VM has been provisioned to allow you to get started contributing right away.  Follow theses instructions and you should have the project up in running in 15 mins:

1.  Install Git (http://git-scm.com/)
2.  Install VirtualBox (https://www.virtualbox.org/wiki/Downloads)
3.  Install Vagrant (https://www.vagrantup.com/downloads.html)
4.  Run some commands to get the provisioned VM and Code Repository

<pre><code>vagrant plugin install vagrant-vbguest
vagrant plugin install vagrant-hostmanager
mkdir hugcommunity.org
cd hugcommunity.org
git clone https://github.com/awelters/Hug-Community-Web-Application.git
curl -OL http://www.hugmehugyou.org/developers/hugcommunity/Vagrantfile
vagrant up</code></pre>

5.  Sit back and wait for the provisioned VM to download and boot...
6.  Verify everything is working correctly by opening your browser and seeing the output

<pre><code>http://www.hugcommunity.org - for the PHP info
http://www.hugcommunity.org/nginx_status - for the NginX statistics
http://www.hugcommunity.org/status?html - for the FPM statistics
http://www.hugcommunity.org/status.html - for the FPM real-time status page
http://www.hugcommunity.org/apc.php - for the APC Cache information page
https://www.hugcommunity.org - the Hug Community Web Application page</code></pre>

7.  Use your favorite PHP IDE for developing the app, changes to the code in the shared <code>/{path_to_where_the_project_is}/hugcommunity/Hug-Community-Web-Application</code> folder will automatically be updated in the <code>/var/www/www.hugcommunity.org</code> directory on the VM
