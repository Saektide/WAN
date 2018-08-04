# Wikia Activity Notifier

* Note: Don't confuse, Wikia Activity Notify is the desktop app, and Notifier is the front-end app.

---

This is the source code of WAN front-end. If you are looking for logging those RC
requests, you can contact to [the cube](https://dev.wikia.com/User_talk:KockaAdmiralac),
WAL (WikiaActivityLogger) is not longer working. However, WAN will restrict some
things like the number of wikis that you added, the HTTPS site, etc. Any bug or
feedback are welcome on Issues.

# Contribute

WAN is made of pure PHP, HTML (templates) and JS. We refuse to use any dependence /
PHP framework. If you want to add some code or want to fix a bug please read this before:

* The JS code must use ES6.
* The site's PHP version is 5.x, not 7.
* Don't change the User-Agent or any other fundamental variable list that looks UPPERCASED,
(e.g MAX_NUMBER_WIKIS).
* Don't change gitlab-ci. We use Travis CI.
* Don't change .htaccess file.
* Don't host this site remotely in any other domain/server. This project has
MIT License.
* Don't make several changes in one commit.

So, how can I run WAN on my machine?

# Installation

WAN can be installed with XAMPP, you can download it [here](https://www.apachefriends.org/download.html).
You must know, WAN is portable but cannot be copied to another domain. Now,
if you want to make changes, you must "fork" this repository.