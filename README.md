# Wikia Activity Notifier

* Note: Don't confuse: Wikia Activity Notify is the desktop app, and Notifier is the front-end app.

---

This is the source code of WAN Front End. If you are looking to log those RC
requests, you can contact to [https://dev.wikia.com/User_talk:KockaAdmiralac][the cube],
WAL (WikiaActivityLogger) is not longer working. However, WAN will restrict some
things like the number of wikis that you added, the HTTPS site, etc. Any bug or
feedback are welcome on Issues.

# Contribute

WAN is made of pure PHP, HTML (templates) and JS. We refuse to use any dependence/
PHP app. If you want to add some code or want to fix a bug please read before this:

* The JS code must use ES6.
* The site's PHP version is 5.x, not 7.
* Don't change User-Agent or other fundamental variable list that seems UPPERCASE,
(example: MAX_NUMBER_WIKIS).
* Don't change gitlab-ci. We use Travis CI.
* Don't change .htaccess file.
* Don't host this site remotely to any other domain/server. This project has
MIT License.
* Don't make several changes in one commit.

So, how can I run WAN on my machine?

# Installation

WAN can be installed in XAMPP, you can download it [https://www.apachefriends.org/download.html][here].
You must know, WAN is portable but cannot be copied to another domain. Now,
if you want to make changes you must "fork" this repository.